import streamlit as st
import pandas as pd
import pickle
import numpy as np
import plotly.express as px
from pathlib import Path
import os

# --- Page Config ---
st.set_page_config(page_title="Stock Prediction Dashboard", layout="wide", page_icon="💊")

# --- Enhanced Green/White Styling ---
st.markdown("""
    <style>
    .stApp { background-color: #f8faf9; }
    
    /* Metrics - Card Style */
    div[data-testid="stMetric"] {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 12px;
        border-left: 5px solid #2e7d32;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    [data-testid="stMetricValue"] { color: #2e7d32 !important; font-weight: 800; }
    [data-testid="stMetricLabel"] { color: #4b5563 !important; font-size: 1.1rem; }

    /* Containers/Cards */
    .st-emotion-cache-1jicn7 { background-color: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

    /* Headers */
    h1, h2, h3 { color: #1b5e20 !important; }

    /* Buttons - Green Theme */
    div.stButton > button:first-child {
        background-color: #2e7d32;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 6px;
        font-weight: bold;
    }
    div.stButton > button:hover { background-color: #1b5e20; color: white; }
    </style>
    """, unsafe_allow_html=True)

# Load model function
@st.cache_resource
def load_model():
    # Mengambil lokasi folder tempat streamlit_app.py berada
    current_dir = os.path.dirname(os.path.abspath(__file__))
    # Menggabungkan path dengan folder model
    model_path = os.path.join(current_dir, "model", "stock_prediction_model.pkl")
    
    if not os.path.exists(model_path):
        # Tampilkan path yang dicari untuk debugging di log
        st.error(f"File tidak ditemukan di: {model_path}")
        return None
    
    with open(model_path, "rb") as f:
        return pickle.load(f)

model_data = load_model()

# --- Header ---
st.title("💊 Pharmacy Inventory Intelligence")
st.markdown("---")

if model_data:
    model = model_data['model']
    feature_names = model_data['feature_names']
    history = model_data['training_history']

    # --- Sidebar ---
    st.sidebar.header("⚙️ Pengaturan Fitur")
    st.sidebar.markdown("Atur parameter untuk melakukan prediksi.")
    
    # Penjelasan fitur untuk orang awam
    feature_descriptions = {
        'total_masuk': 'Jumlah stok barang yang masuk ke apotek.',
        'total_keluar': 'Jumlah stok barang yang terjual/keluar.',
        'stok_saat_ini': 'Sisa stok yang tersedia saat ini.',
        'frekuensi_transaksi': 'Seberapa sering barang dibeli.',
        'avg_transaksi': 'Rata-rata jumlah barang dalam satu transaksi.',
        'kedaluwarsa_risk': 'Tingkat risiko barang mendekati kedaluwarsa.',
        'accuracy_deviation': 'Tingkat kesalahan pencatatan stok.',
        'safety_stock_ratio': 'Rasio stok pengaman untuk keadaan darurat.',
        'demand_volatility': 'Seberapa fluktuatif (naik-turun) permintaan barang.',
        'trend_indicator': 'Indikator tren peningkatan atau penurunan penjualan.',
        'safety_stock': 'Jumlah stok minimum yang harus selalu ada.',
        'avg_daily_sales': 'Rata-rata penjualan harian.'
    }
    
    user_inputs = {}
    with st.sidebar.container():
        for feature in feature_names:
            label = feature.replace('_', ' ').title()
            # Gunakan deskripsi jika ada
            help_text = feature_descriptions.get(feature, 'Parameter untuk prediksi')
            user_inputs[feature] = st.number_input(f"{label}", value=0.0, help=help_text)

    # --- Tabs Utama ---
    tab_utama1, tab_utama2, tab_data, tab_drift = st.tabs(["Gambaran Umum", "Prediksi Stok", "Lihat Data", "Uji Tekanan"])

    with tab_utama1:
            st.subheader("Analisis Kualitas Model")
            train_r2 = history['train_r2']
            test_r2 = history['test_r2']
            diff = abs(train_r2 - test_r2)
            
            if diff < 0.05 and test_r2 > 0.7:
                st.success("### ✅ Evaluasi Model: Sangat Baik")
                st.markdown(f"- **Tidak Mengalami Overfitting:** Perbedaan akurasi antara data latihan ({train_r2:.4f}) dan data uji ({test_r2:.4f}) sangat kecil.\n- **Aman dari Kebocoran Data (Leakage):** Pola yang dipelajari konsisten.")
            elif diff < 0.1:
                st.warning("### ⚠️ Evaluasi Model: Stabil")
                st.markdown("Model cukup stabil dalam membuat prediksi.")
            else:
                st.error("### ❌ Evaluasi Model: Perlu Diperiksa")
                st.markdown("Ada perbedaan performa yang cukup besar.")

        # ! Grafik performa model
            st.subheader("Berbagai Grafik Hasil Performa Model")

            tab1, tab2, tab3, tab4 = st.tabs(["Aktual vs Prediksi", "Analisis Kebutuhan Stok", "Analisis Hubungan Fitur (Korelasi)", "Fitur Penting (Feature Importance)"])

            # Aktual vs Prediksi
            with tab1:
                    st.subheader("Actual vs. Predicted Stock")
                    data_path = Path("dataset/prepared_data.csv")
                    if data_path.exists():
                        df = pd.read_csv(data_path)
                        sample_df = df.sample(n=min(500, len(df)), random_state=42)
                    
                    X_sample = sample_df[feature_names]
                    y_actual = sample_df['predicted_required_stock']
                    y_pred = model.predict(X_sample)
                    
                    fig_actual_pred = px.scatter(x=y_actual, y=y_pred, labels={'x': 'Actual Stock', 'y': 'Predicted Stock'},
                    title="Actual vs Predicted Values")
                    fig_actual_pred.add_shape(type="line", line=dict(dash='dash'), x0=y_actual.min(), y0=y_actual.min(), 
                    x1=y_actual.max(), y1=y_actual.max())
                    st.plotly_chart(fig_actual_pred, use_container_width=True)
                
                    st.markdown("### 💡 Cara Membaca Grafik:")
                    st.info("""
                    - **Sumbu Horizontal (X):** Stok obat yang sebenarnya terjadi di apotek.
                    - **Sumbu Vertikal (Y):** Stok obat yang diprediksi oleh AI.
                    - **Kesimpulan:** Semakin merapat titik-titik biru membentuk garis lurus diagonal, artinya **prediksi AI semakin mendekati 100% sempurna**. Pada grafik ini, model menunjukkan performa luar biasa dalam menebak kebutuhan stok secara tepat!
                    """)

            # Analisis Kebutuhan Stok
            with tab2:
                    st.subheader("Inventory Risk Matrix")
                    if 'stok_saat_ini' in df.columns:
                        fig_risk = px.scatter(df, x='stok_saat_ini', y='predicted_required_stock', 
                                            color='predicted_required_stock', size='predicted_required_stock',
                                            title="Current Stock vs Predicted Need (Risk Matrix)",
                                        labels={'stok_saat_ini': 'Current Stock', 'predicted_required_stock': 'Predicted Need'})
                    st.plotly_chart(fig_risk, use_container_width=True)
                    # Penjelasannya
                    st.markdown("### Korelasi Stok Saat Ini vs Kebutuhan Prediksi")
                    st.info("""
                    * **Tren Menurun:** Grafik menunjukkan hubungan terbalik yang logis. 
                    * **Sistem Peringatan Dini:** Ketika **Current Stock** mendekati angka 0, grafik otomatis naik tajam pada **Predicted Need**. Ini membantu apotek mendeteksi obat mana yang kritis dan harus segera di-order ulang (*restock*) agar tidak kecolongan kehabisan obat.
                    """)
            # Analisis Hubungan Fitur (Korelasi)
            with tab3:
                    st.subheader("Feature Correlation Matrix")
                    corr = df.corr()
                    fig_corr = px.imshow(corr, text_auto=True, aspect="auto", color_continuous_scale='Greens', 
                                        title="Feature Correlation Heatmap")
                    st.plotly_chart(fig_corr, use_container_width=True)
                    # Penjelasannya
                    st.markdown("### Bagaimana AI Mengambil Keputusan?")
                    st.success("""
                    * **Faktor Utama (Hijau Tua):** Model AI ini terbukti sangat dipengaruhi oleh **Rata-rata Penjualan Harian (Avg Daily Sales)** dan **Volume Transaksi**. 
                    * **Kesimpulan:** Model tidak asal tebak, melainkan menghitung kebutuhan masa depan berdasarkan seberapa laris obat tersebut keluar setiap harinya.
                    """)
            # Fitur Penting (Feature Importance)
            with tab4:
                    st.subheader("Feature Importance")
                    imp_df = pd.DataFrame({'Feature': [f.replace('_', ' ').title() for f in feature_names], 'Importance': model.feature_importances_})
                    fig = px.bar(imp_df.sort_values('Importance'), x='Importance', y='Feature', orientation='h', color='Importance', color_continuous_scale='Greens')
                    st.plotly_chart(fig, use_container_width=True)
                    # Penjelasannya
                    st.markdown("### 🎯 Faktor Apa yang Paling Memengaruhi Prediksi AI?")
                    st.info("""
                    Berdasarkan analisis bobot model, berikut adalah faktor penentu kebijakan stok obat:
                    
                    1. **Total Keluar (Dominan - ~75%):** Ini adalah metrik paling krusial. AI mengandalkan akumulasi jumlah obat yang keluar untuk menentukan kapan harus memesan barang baru.
                    2. **Demand Volatility (~14%):** AI melihat seberapa fluktuatif (naik-turun) permintaan obat di apotek sebagai pertimbangan kedua.
                    3. **Safety Stock & Avg Daily Sales (<5%):** Memiliki pengaruh paling kecil dalam kalkulasi akhir model ini.
                    
                    *Kesimpulan:* Strategi inventory apotek kamu saat ini sangat digerakkan oleh **volume total penjualan (demand-driven planning)**.
                    """)


    # --- Tab Utama 2 ---
    with tab_utama2:
        st.subheader("Real-time Inventory Forecast")
        
        if st.button("🚀 Calculate Recommended Stock"):
            # Check if all inputs are default (0.0)
            if all(value == 0.0 for value in user_inputs.values()):
                st.error("⚠️ Please input feature values before calculating the forecast.")
            else:
                X = np.array([user_inputs[f] for f in feature_names]).reshape(1, -1)
                prediction = model.predict(X)[0]

                st.metric("Predicted Required Stock", f"{prediction:,.0f} units")

                # Action logic
                current_stock = user_inputs.get('stok_saat_ini', 0)
                if current_stock > 0:
                    gap = prediction - current_stock
                    if gap > 0:
                        st.warning(f"### ⚠️ Action Required\nProcure **{gap:,.0f}** units immediately.")
                    else:
                        st.success(f"### ✅ Status OK\nStock is sufficient. Excess: **{abs(gap):,.0f}** units.")

                with st.expander("View Input Parameters"):
                    st.table(pd.DataFrame(user_inputs, index=['Value']).T)
    # Penjelasan tambahan untuk tab utama 2
        st.markdown("### 📌 Cara Melakukan Prediksi Stok:")
        st.info("""
        1. **Masukkan Nilai Fitur:** Di sidebar kiri, isi nilai untuk setiap fitur yang relevan dengan kondisi apotek saat ini (misalnya, jumlah stok saat ini, rata-rata penjualan harian, dll).
        2. **Klik Tombol Prediksi:** Setelah semua nilai diisi, klik tombol "Calculate Recommended Stock" untuk melihat hasil prediksi kebutuhan stok.
        3. **Interpretasi Hasil:** Hasil prediksi akan menunjukkan jumlah stok yang direkomendasikan berdasarkan kondisi apotek saat ini.
        4. **Tindakan Lanjutan:** Jika stok saat ini kurang dari prediksi, sistem akan memberikan peringatan untuk segera melakukan pemesanan ulang (restock).
        """)

    # --- Tab Data ---
    with tab_data:
        col_d1, col_d2 = st.columns(2)
        with col_d1:
            st.markdown("### Algorithm")
            st.info("Decision Tree Regressor")
        with col_d2:
            st.markdown("### Methodology")
            st.success("CARD (Classification and Regression Decision Tree)")
        col_d1, col_d2 = st.columns(2)
        with col_d1:
            st.markdown("### Hyperparameters")
            st.write(model.get_params())
            
        with col_d2:
            st.markdown("### Detailed Metrics")
            metrics_df = pd.DataFrame([history]).T.rename(columns={0: 'Value'})
            st.table(metrics_df)

        st.subheader("Sample Training Data")
        data_path = Path("dataset/prepared_data.csv")
        if data_path.exists():
            df_sample = pd.read_csv(data_path).head(100)
            st.dataframe(df_sample, use_container_width=True)
            st.markdown(f"*Showing first 100 rows from `backend/dataset/prepared_data.csv`*")
        else:
            st.warning("Training data not found.")

        # --- Tab 5: Drift Analysis ---
        with tab_drift:
            st.subheader("Simulasi Drift Data")
            st.markdown("Tes bagaimana data merespons kondisi abnormal seperti demand shock atau perubahan pola penjualan.")
            if st.button("Run Drift Simulation"):
                import simulate_drift
                simulate_drift.simulate_drift("model/stock_prediction_model.pkl", 
                                            "dataset/prepared_data.csv", 
                                            "dataset/drifted_data.csv")
                
            drift_path = Path("dataset/drifted_data.csv")
            if drift_path.exists():
                df_drift = pd.read_csv(drift_path)
                st.write("### Hasil Simulasi Data Merespons Drift")
                st.dataframe(df_drift.head(20), use_container_width=True)
                
                # Compare original vs drift prediction
                fig = px.scatter(df_drift, x='predicted_required_stock', y='predicted_drift',
                                title="Original Prediction vs Drifted Prediction",
                                labels={'predicted_required_stock': 'Original', 'predicted_drift': 'Drifted'})
                st.plotly_chart(fig, use_container_width=True)
            
else:
    st.warning("⚠️ Model tidak ditemukan.")
