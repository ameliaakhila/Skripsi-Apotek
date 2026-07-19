"""
Streamlit Dashboard — Apotek Stock Prediction
Menggunakan Hugging Face Spaces API sebagai backend ML
(tidak lagi load pkl lokal — menghindari error versi scikit-learn)

HF Space : https://ameliaakhila29-apotek-stock-prediction.hf.space
"""

import streamlit as st
import pandas as pd
import numpy as np
import plotly.express as px
import plotly.graph_objects as go
import requests
from pathlib import Path
import os
import time
import base64

# ─────────────────────────────────────────────────────────────────────────────
# KONFIGURASI
# ─────────────────────────────────────────────────────────────────────────────
HF_API_URL = "https://ameliaakhila29-apotek-stock-prediction.hf.space"
# LOCAL_API_URL = "http://localhost:7860"

# 8 Fitur model (harus konsisten dengan training)
FEATURE_NAMES = [
    'total_masuk',
    'total_keluar',
    'stok_saat_ini',
    'frekuensi_transaksi',
    'avg_transaksi',
    'demand_volatility',
    'avg_daily_sales',
    'safety_stock',
]

FEATURE_DESCRIPTIONS = {
    'total_masuk'         : 'Total stok barang yang pernah masuk ke apotek (historis).',
    'total_keluar'        : 'Total stok barang yang terjual/keluar (historis).',
    'stok_saat_ini'       : 'Sisa stok yang tersedia saat ini di gudang.',
    'frekuensi_transaksi' : 'Seberapa sering transaksi terjadi dalam 3 bulan terakhir.',
    'avg_transaksi'       : 'Rata-rata jumlah barang per satu transaksi.',
    'demand_volatility'   : 'Seberapa fluktuatif (naik-turun) permintaan barang harian.',
    'avg_daily_sales'     : 'Rata-rata penjualan harian berdasarkan 3 bulan terakhir.',
    'safety_stock'        : 'Jumlah stok minimum yang harus selalu tersedia.',
}

# ─────────────────────────────────────────────────────────────────────────────
# PAGE CONFIG
# ─────────────────────────────────────────────────────────────────────────────
st.set_page_config(
    page_title="Apotek Stock Prediction Dashboard",
    layout="wide",
    page_icon="favicon.ico",
    initial_sidebar_state="expanded"
)

# ─────────────────────────────────────────────────────────────────────────────
# CSS STYLING
# ─────────────────────────────────────────────────────────────────────────────
st.markdown("""
    <style>
    .stApp { background-color: #f0f4f0; }

    /* Metric cards */
    div[data-testid="stMetric"] {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 12px;
        border-left: 5px solid #2e7d32;
        box-shadow: 0 4px 8px rgba(0,0,0,0.06);
    }
    [data-testid="stMetricValue"] { color: #2e7d32 !important; font-weight: 800; }
    [data-testid="stMetricLabel"] { color: #4b5563 !important; font-size: 1rem; }

    /* Headers */
    h1, h2, h3 { color: #1b5e20 !important; }

    /* Buttons */
    div.stButton > button:first-child {
        background-color: #2e7d32;
        color: white;
        border: none;
        padding: 10px 28px;
        border-radius: 8px;
        font-weight: bold;
        font-size: 1rem;
    }
    div.stButton > button:hover { background-color: #1b5e20; }

    /* API status badge */
    .api-online  { background:#d4edda; color:#155724; padding:6px 14px; border-radius:20px; font-weight:bold; }
    .api-offline { background:#f8d7da; color:#721c24; padding:6px 14px; border-radius:20px; font-weight:bold; }
    .api-loading { background:#fff3cd; color:#856404; padding:6px 14px; border-radius:20px; font-weight:bold; }
    </style>
""", unsafe_allow_html=True)

def get_base64_image(image_path):
    with open(image_path, "rb") as image_file:
        encoded = base64.b64encode(image_file.read()).decode()
    return f"data:image/png;base64,{encoded}"


# ─────────────────────────────────────────────────────────────────────────────
# FUNGSI API — SEMUA INTERAKSI LEWAT HF SPACES
# ─────────────────────────────────────────────────────────────────────────────

@st.cache_resource(ttl=300)  # Cache 5 menit
def get_model_info():
    """Ambil info model dari HF API"""
    try:
        resp = requests.get(f"{HF_API_URL}/model/info", timeout=30)
        if resp.status_code == 200:
            return resp.json()
    except Exception as e:
        st.warning(f"Tidak bisa ambil info model: {e}")
    return None


@st.cache_resource(ttl=60)
def check_api_health():
    """Cek apakah HF API online"""
    try:
        resp = requests.get(f"{HF_API_URL}/status", timeout=15)
        if resp.status_code == 200:
            data = resp.json()
            return data.get('status') == 'healthy', data
    except Exception:
        pass
    return False, {}


def call_predict_api(features: dict) -> dict | None:
    """Panggil /predict ke HF API"""
    try:
        resp = requests.post(
            f"{HF_API_URL}/predict",
            json=features,
            timeout=30
        )
        if resp.status_code == 200:
            return resp.json()
        else:
            st.error(f"API error {resp.status_code}: {resp.text}")
    except requests.exceptions.ConnectionError:
        st.error("❌ Tidak bisa terhubung ke HF API. Pastikan Space sudah aktif.")
    except requests.exceptions.Timeout:
        st.error("⏳ API timeout. HF Space mungkin sedang cold start — coba lagi dalam 30 detik.")
    except Exception as e:
        st.error(f"Error API: {e}")
    return None


def call_predict_batch_api(items: list) -> dict | None:
    """Panggil /predict/batch ke HF API"""
    try:
        resp = requests.post(
            f"{HF_API_URL}/predict/batch",
            json={"predictions": items},
            timeout=60
        )
        if resp.status_code == 200:
            return resp.json()
    except Exception as e:
        st.error(f"Batch API error: {e}")
    return None


# ─────────────────────────────────────────────────────────────────────────────
# HEADER
# ─────────────────────────────────────────────────────────────────────────────
col_title, col_status = st.columns([4, 1])
with col_title:
    # Buat kolom untuk ikon dan teks judul
    col_icon, col_text = st.columns([1, 3], gap="medium")
    with col_icon:
        img_base64 = get_base64_image("logo.png")
        st.markdown(f"<img src='{img_base64}' width='50' />", unsafe_allow_html=True)
    with col_text:
        st.title("Pharmacy Inventory Intelligence")
with col_status:
    with st.spinner("Cek API..."):
        api_online, api_status = check_api_health()

    if api_online:
        st.markdown('<span class="api-online">🟢 API Online</span>', unsafe_allow_html=True)
        model_name = api_status.get('model_loaded', False)
        if model_name:
            st.caption(f"Model: ✅ Loaded")
        else:
            st.caption("Model: ⏳ Loading...")
    else:
        st.markdown('<span class="api-offline">🔴 API Offline</span>', unsafe_allow_html=True)
        st.caption(f"URL: {HF_API_URL}")

st.markdown("---")

# ─────────────────────────────────────────────────────────────────────────────
# SIDEBAR — INPUT FITUR
# ─────────────────────────────────────────────────────────────────────────────
st.sidebar.header("Input Fitur Prediksi")
st.sidebar.markdown("Masukkan data apotek untuk mendapatkan prediksi stok.")
st.sidebar.markdown("---")

user_inputs = {}
for feature in FEATURE_NAMES:
    label    = feature.replace('_', ' ').title()
    helptext = FEATURE_DESCRIPTIONS.get(feature, '')
    user_inputs[feature] = st.sidebar.number_input(
        label,
        value=st.session_state.get(f"input_{feature}", 0.0),
        min_value=0.0,
        step=1.0,
        help=helptext,
        key=f"input_{feature}"
    )

# ─────────────────────────────────────────────────────────────────────────────
# MAIN TABS
# ─────────────────────────────────────────────────────────────────────────────
tab_overview, tab_predict, tab_model, tab_stress = st.tabs([
    "Gambaran Umum",
    "Prediksi Stok",
    "Info Model",
    "Tes Ketahanan Model"
])


# ══════════════════════════════════════════════════════
# TAB 1 — GAMBARAN UMUM
# ══════════════════════════════════════════════════════
with tab_overview:
    st.subheader("Gambaran Umum Sistem Prediksi")

    # Ambil info model
    model_info = get_model_info()

    if model_info:
        col1, col2, col3, col4 = st.columns(4)
        with col1:
            st.metric("Algoritma", "Decision Tree")
        with col2:
            st.metric("Metode", "CARD")
        with col3:
            features_count = len(model_info.get('feature_names', FEATURE_NAMES))
            st.metric("Jumlah Fitur", features_count)
        with col4:
            r2 = model_info.get('performance', {}).get('test_r2', 0)
            if isinstance(r2, float):
                st.metric("R² Score (Test)", f"{r2:.4f}")
            else:
                st.metric("R² Score (Test)", str(r2))

        # Performa model
        perf = model_info.get('performance', {})
        if perf:
            train_r2 = perf.get('train_r2', 0)
            test_r2  = perf.get('test_r2', 0)
            diff = abs(train_r2 - test_r2)

            if diff < 0.05 and test_r2 > 0.7:
                st.success(f"""
                ### ✅ Evaluasi Model: Sangat Baik
                - **Tidak Overfitting:** Selisih Train R² ({train_r2:.4f}) vs Test R² ({test_r2:.4f}) = **{diff:.4f}** (sangat kecil)
                - **Akurasi Tinggi:** Model mampu memprediksi kebutuhan stok dengan sangat tepat
                """)
            elif diff < 0.1:
                st.warning(f"### ⚠️ Evaluasi Model: Stabil\nSelisih R²: {diff:.4f} — model cukup konsisten.")
            else:
                st.error(f"### ❌ Model Perlu Ditinjau\nSelisih R²: {diff:.4f} — kemungkinan overfitting.")

            # Metrics detail
            st.subheader("📈 Metrik Evaluasi Model")
            col_m1, col_m2 = st.columns(2)
            with col_m1:
                st.markdown("**Training Set**")
                st.metric("R² Score", f"{train_r2:.4f}")
                st.metric("MAE", f"{perf.get('train_mae', 0):.4f} unit")

            with col_m2:
                st.markdown("**Test Set**")
                st.metric("R² Score", f"{test_r2:.4f}")
                st.metric("MAE", f"{perf.get('test_mae', 0):.4f} unit")

        # Feature importance (dari API)
        feat_imp = model_info.get('feature_importance', {})
        if feat_imp:
            st.markdown("---")
            st.subheader("🎯 Kepentingan Fitur (Feature Importance)")
            st.subheader("Kepentingan Fitur (Feature Importance)")

            imp_df = pd.DataFrame([
                {'Fitur': k.replace('_', ' ').title(), 'Importance': v}
                for k, v in sorted(feat_imp.items(), key=lambda x: x[1], reverse=True)
            ])

            fig_imp = px.bar(
                imp_df.sort_values('Importance'),
                x='Importance', y='Fitur',
                orientation='h',
                color='Importance',
                color_continuous_scale='Greens',
                title='Seberapa Besar Pengaruh Tiap Fitur Terhadap Prediksi',
                labels={'Importance': 'Tingkat Kepentingan', 'Fitur': 'Fitur'}
            )
            st.plotly_chart(fig_imp, use_container_width=True)

            st.info("""
            **Cara Membaca:** Semakin panjang batang hijau, semakin besar pengaruh fitur tersebut dalam menentukan prediksi stok.
            Model ini sangat dipengaruhi oleh **avg_transaksi** dan **safety_stock** — artinya volume transaksi
            dan batas minimum stok adalah faktor paling krusial dalam prediksi.
            """)

        st.markdown("---")
        st.subheader("Visualisasi Struktur Root Node Decision Tree")

        tree_url = f"{HF_API_URL}/model/tree-image"
        try:
            with st.spinner("Mengunduh gambar struktur pohon..."):
                response = requests.get(tree_url, timeout=20)
                if response.status_code == 200:
                    st.image(response.content, caption="Struktur Root Node Decision Tree (dari API)", use_container_width=True)
                else:
                    st.warning(f"Gagal memuat visualisasi pohon (Status {response.status_code}). API mungkin sedang sibuk.")
        except Exception as e:
            st.error(f"Gagal menghubungi server API untuk mengambil gambar: {e}")

        # Demonstrasi scatter prediksi (simulasi)
        st.markdown("---")
        st.subheader("Simulasi: Actual vs Predicted")
        st.info(
            "**Catatan:** Grafik di bawah adalah **simulasi** untuk ilustrasi, bukan dari data uji nyata. "
            "Ini menunjukkan bagaimana sebaran titik akan terlihat jika model memiliki akurasi (R²) yang sangat tinggi."
        )

        np.random.seed(42)
        n_points = 200
        actual   = np.random.exponential(scale=50, size=n_points) + 10
        noise    = np.random.normal(0, actual * 0.03, n_points)  # 3% noise
        predicted = actual + noise

        fig_scatter = px.scatter(
            x=actual, y=predicted,
            labels={'x': 'Nilai Aktual (unit)', 'y': 'Nilai Prediksi (unit)'},
            title=f'Simulasi Actual vs Predicted Stock (R² ≈ {r2:.4f})' if isinstance(r2, float) else 'Simulasi Actual vs Predicted Stock',
            opacity=0.6, color_discrete_sequence=['#2e7d32']
        )
        # Garis diagonal sempurna
        fig_scatter.add_shape(
            type='line', line=dict(dash='dash', color='red', width=2),
            x0=actual.min(), y0=actual.min(),
            x1=actual.max(), y1=actual.max()
        )
        st.plotly_chart(fig_scatter, use_container_width=True)
        st.caption("Titik-titik mendekati garis merah = prediksi mendekati sempurna (simulasi ilustrasi, bukan data real)")

    else:
        if not api_online:
            st.warning("⚠️ API sedang offline. Menunggu HF Space aktif... (bisa butuh 1-2 menit cold start)")
            if st.button("🔄 Refresh Status API"):
                st.cache_resource.clear()
                st.rerun()
        else:
            st.info("Memuat info model dari API...")


# ══════════════════════════════════════════════════════
# TAB 2 — PREDIKSI STOK
# ══════════════════════════════════════════════════════
with tab_predict:
    st.subheader("Prediksi Kebutuhan Stok Obat")
    st.markdown("""
    Masukkan parameter di **sidebar kiri**, lalu klik tombol di bawah untuk mendapatkan prediksi
    kebutuhan stok 1 bulan ke depan berdasarkan riwayat 3 bulan terakhir.
    """)

    col_btn, col_info = st.columns([1, 3])
    with col_btn:
        btn_predict = st.button("Hitung Prediksi Stok", type="primary", use_container_width=True)

    with col_info:
        st.info("**Catatan:** Prediksi = Kebutuhan stok 1 bulan ke depan. Rekomendasi Order = berapa unit yang perlu dipesan sekarang.")

    if btn_predict:
        # Validasi input
        if all(v == 0.0 for v in user_inputs.values()):
            st.error("⚠️ Semua nilai masih 0. Isi setidaknya beberapa fitur terlebih dahulu, atau gunakan tombol 'Isi Contoh Data' di sidebar.")
        elif not api_online:
            st.error("❌ API tidak tersedia. Pastikan HF Space sudah aktif.")
        else:
            with st.spinner("Menghubungi Hugging Face API..."):
                result = call_predict_api(user_inputs)

            if result:
                predicted = result.get('predicted_stock', 0)
                confidence = result.get('confidence', 'medium')
                rekomendasi_order = result.get('rekomendasi_order', max(0, predicted - user_inputs.get('stok_saat_ini', 0)))
                recommendation    = result.get('rekomendasi_order_text', result.get('recommendation', '-'))

                st.markdown("---")
                st.subheader("Hasil Prediksi")

                col_r1, col_r2, col_r3 = st.columns(3)
                with col_r1:
                    st.metric(
                        "Kebutuhan 1 Bulan",
                        f"{predicted:,.0f} unit",
                        delta=f"{predicted - user_inputs.get('stok_saat_ini', 0):+.0f} vs stok saat ini"
                    )
                with col_r2:
                    st.metric(
                        "Rekomendasi Order",
                        f"{rekomendasi_order:,.0f} unit",
                        delta="Segera pesan" if rekomendasi_order > 0 else "Tidak perlu pesan"
                    )
                with col_r3:
                    conf_label = {"high": "🟢 Tinggi", "medium": "🟡 Sedang", "low": "🔴 Rendah"}.get(confidence, confidence)
                    st.metric("Tingkat Kepercayaan", conf_label)

                # Rekomendasi aksi
                gap = predicted - user_inputs.get('stok_saat_ini', 0)
                if gap > user_inputs.get('safety_stock', 10) * 2:
                    st.error(f"🚨 **KRITIS:** {recommendation}")
                elif gap > 0:
                    st.warning(f"⚠️ **Perlu Restok:** {recommendation}")
                else:
                    st.success(f"✅ **Stok Aman:** {recommendation}")

                # Visualisasi gauge
                fig_gauge = go.Figure(go.Indicator(
                    mode="gauge+number+delta",
                    value=user_inputs.get('stok_saat_ini', 0),
                    delta={'reference': predicted, 'valueformat': '.0f'},
                    title={'text': "Stok Saat Ini vs Kebutuhan (unit)"},
                    gauge={
                        'axis': {'range': [0, max(predicted * 1.5, user_inputs.get('stok_saat_ini', 10) * 1.5)]},
                        'bar': {'color': "#2e7d32"},
                        'steps': [
                            {'range': [0, user_inputs.get('safety_stock', 10)], 'color': '#ffcdd2'},
                            {'range': [user_inputs.get('safety_stock', 10), predicted], 'color': '#fff9c4'},
                            {'range': [predicted, predicted * 1.5], 'color': '#c8e6c9'},
                        ],
                        'threshold': {
                            'line': {'color': 'red', 'width': 4},
                            'thickness': 0.75,
                            'value': predicted
                        }
                    }
                ))
                st.plotly_chart(fig_gauge, use_container_width=True)

                # Detail input
                with st.expander("Lihat Parameter Input"):
                    input_df = pd.DataFrame([{
                        'Fitur': k.replace('_', ' ').title(),
                        'Nilai': v,
                        'Keterangan': FEATURE_DESCRIPTIONS.get(k, '-')
                    } for k, v in user_inputs.items()])
                    st.dataframe(input_df, use_container_width=True, hide_index=True)

    # Panduan penggunaan
    st.markdown("---")
    with st.expander("Panduan Cara Menggunakan"):
        st.markdown("""
        1. **Isi nilai fitur** di sidebar kiri sesuai kondisi obat di apotek
        2. **Klik tombol** "Hitung Prediksi Stok"
        3. **Baca hasil:**
           - **Kebutuhan 1 Bulan** = berapa unit yang harus tersedia sebulan ke depan
           - **Rekomendasi Order** = berapa unit yang perlu dipesan sekarang
           - **Kepercayaan Tinggi** = data transaksi cukup untuk prediksi akurat
        4. **Tindak lanjut:** Jika ada peringatan kritis, segera lakukan pemesanan ke supplier
        """)


# ══════════════════════════════════════════════════════
# TAB 3 — INFO MODEL
# ══════════════════════════════════════════════════════
with tab_model:
    st.subheader("Informasi Model ML")

    model_info = get_model_info()

    col_algo, col_data = st.columns(2)

    with col_algo:
        st.markdown("### Algoritma & Metode")
        st.info("""
        - **Algoritma:** Decision Tree Regressor
        - **Metode:** CARD (Classification and Regression Decision Tree)
        - **Platform:** Hugging Face Spaces (Docker)
        - **Backend:** Flask REST API
        """)

        st.markdown("### Periode Data Training")
        st.success("""
        - **Sumber:** Database transaksi apotek nyata
        - **Periode fitur:** 3 bulan terakhir
        - **Fitur target:** Kebutuhan stok 1 bulan ke depan
        """)

    with col_data:
        if model_info:
            st.markdown("### Hyperparameter Model")
            params = model_info.get('model_params', {})
            if params:
                params_df = pd.DataFrame(
                    [{'Parameter': k, 'Nilai': str(v)} for k, v in params.items()]
                )
                st.dataframe(params_df, use_container_width=True, hide_index=True)
            else:
                st.info("Parameter tidak tersedia dari API")

            st.markdown("### Info Teknis API")
            api_meta = {
                'URL': HF_API_URL,
                'Endpoint Prediksi': '/predict',
                'Endpoint Batch': '/predict/batch',
                'Status': '🟢 Online' if api_online else '🔴 Offline',
                'Fitur Model': len(model_info.get('feature_names', FEATURE_NAMES)),
            }
            api_df = pd.DataFrame([{'Info': k, 'Detail': str(v)} for k, v in api_meta.items()])
            st.dataframe(api_df, use_container_width=True, hide_index=True)
        else:
            st.warning("Info model tidak bisa diambil dari API saat ini.")

    # Penjelasan fitur
    st.markdown("---")
    st.subheader("Penjelasan 8 Fitur Model")
    feat_df = pd.DataFrame([
        {
            'No': i + 1,
            'Nama Fitur': f.replace('_', ' ').title(),
            'Kode': f,
            'Keterangan': FEATURE_DESCRIPTIONS[f]
        }
        for i, f in enumerate(FEATURE_NAMES)
    ])
    st.dataframe(feat_df, use_container_width=True, hide_index=True)

    st.markdown("---")
    st.subheader("📦 Cara Deploy Model Baru")
    st.code("""
# 1. Export data training dari database Laravel
cd frontend && php export_training_data.php

# 2. Latih ulang model
cd backend
python notebook/model_trainer.py

# 3. Copy model ke folder HF
cp model/stock_prediction_model.pkl ../apotek-stock-prediction/model/

# 4. Push ke Hugging Face
cd ../apotek-stock-prediction
git add model/stock_prediction_model.pkl
git commit -m "Update model"
git push origin main
    """, language="bash")


# ══════════════════════════════════════════════════════
# TAB 4 — UJI STRES (BATCH PREDICTION)
# ══════════════════════════════════════════════════════
with tab_stress:
    st.subheader("Tes Ketahanan Model")
    st.markdown("""
    Simulasi prediksi untuk banyak obat sekaligus menggunakan API batch.
    Berguna untuk menguji performa dan stabilitas model.
    """)

    col_cfg1, col_cfg2 = st.columns(2)
    with col_cfg1:
        n_items = st.slider("Jumlah obat yang disimulasikan", min_value=5, max_value=50, value=10, step=5)
    with col_cfg2:
        seed = st.number_input("Random seed", value=42, step=1)

    if st.button("Jalankan Simulasi Batch", use_container_width=True):
        np.random.seed(int(seed))

        # Generate random items
        items = []
        for i in range(n_items):
            stok_masuk   = int(np.random.randint(100, 1000))
            stok_keluar  = int(np.random.randint(10, stok_masuk))
            stok_saat_ini = max(0, stok_masuk - stok_keluar)
            items.append({
                'id_obat'             : i + 1,
                'total_masuk'         : float(stok_masuk),
                'total_keluar'        : float(stok_keluar),
                'stok_saat_ini'       : float(stok_saat_ini),
                'frekuensi_transaksi' : float(np.random.randint(0, 30)),
                'avg_transaksi'       : float(round(np.random.uniform(1, 20), 2)),
                'demand_volatility'   : float(round(np.random.uniform(0, 10), 2)),
                'avg_daily_sales'     : float(round(np.random.uniform(0, 5), 2)),
                'safety_stock'        : float(np.random.randint(5, 50)),
            })

        with st.spinner(f"Mengirim {n_items} prediksi ke HF API..."):
            start_time = time.time()
            result = call_predict_batch_api(items)
            elapsed = time.time() - start_time

        if result and 'results' in result:
            st.success(f"✅ Selesai dalam **{elapsed:.2f} detik** | {n_items} obat diproses")

            results_list = result['results']

            # Build DataFrame hasil
            rows = []
            for item, res in zip(items, results_list):
                if 'error' not in res:
                    pred = res.get('predicted_stock', 0)
                    order = res.get('rekomendasi_order', max(0, pred - item['stok_saat_ini']))
                    rows.append({
                        'ID Obat'            : item['id_obat'],
                        'Stok Saat Ini'      : int(item['stok_saat_ini']),
                        'Kebutuhan 1 Bulan'  : int(pred),
                        'Rekomendasi Order'  : int(order),
                        'Kepercayaan'        : res.get('confidence', '-'),
                        'Status'             : '🚨 Kritis' if order > item['safety_stock'] * 2 else ('⚠️ Perlu Order' if order > 0 else '✅ Aman'),
                    })

            if rows:
                df_result = pd.DataFrame(rows)
                st.dataframe(df_result, use_container_width=True, hide_index=True)

                # Chart distribusi
                col_c1, col_c2 = st.columns(2)
                with col_c1:
                    fig_dist = px.histogram(
                        df_result, x='Kebutuhan 1 Bulan',
                        title='Distribusi Kebutuhan Stok',
                        color_discrete_sequence=['#2e7d32'],
                        nbins=15
                    )
                    st.plotly_chart(fig_dist, use_container_width=True)

                with col_c2:
                    status_count = df_result['Status'].value_counts().reset_index()
                    status_count.columns = ['Status', 'Jumlah']
                    fig_pie = px.pie(
                        status_count, values='Jumlah', names='Status',
                        title='Distribusi Status Stok',
                        color_discrete_sequence=['#2e7d32', '#ff9800', '#f44336']
                    )
                    st.plotly_chart(fig_pie, use_container_width=True)

                # Scatter: stok saat ini vs prediksi
                fig_sc = px.scatter(
                    df_result,
                    x='Stok Saat Ini', y='Kebutuhan 1 Bulan',
                    color='Status', size='Rekomendasi Order',
                    title='Stok Saat Ini vs Kebutuhan 1 Bulan',
                    color_discrete_map={
                        '✅ Aman': '#2e7d32',
                        '⚠️ Perlu Order': '#ff9800',
                        '🚨 Kritis': '#f44336'
                    }
                )
                st.plotly_chart(fig_sc, use_container_width=True)
        else:
            st.error("Gagal mendapatkan hasil dari API. Pastikan HF Space sudah aktif.")


# ─────────────────────────────────────────────────────────────────────────────
# FOOTER
# ─────────────────────────────────────────────────────────────────────────────
img_base64 = get_base64_image("logo.png")
st.markdown("---")
st.markdown(f"""
<div style='text-align:center; color:#666; font-size:0.85rem;'>
<b><img src="{img_base64}" width="10"> Apotek Stock Prediction Dashboard</b> | 
Model: Decision Tree Regressor (CARD) | 
API: <a href='{HF_API_URL}/status' target='_blank'>Hugging Face Spaces</a> |
8 Fitur | Data: 3 Bulan Terakhir
</div>
""", unsafe_allow_html=True)
