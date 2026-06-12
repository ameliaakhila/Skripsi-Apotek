import streamlit as st
import pandas as pd
import pickle
import numpy as np
import plotly.express as px
from pathlib import Path

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
    model_path = Path("model/stock_prediction_model.pkl")
    if not model_path.exists():
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
    st.sidebar.header("⚙️ Feature Inputs")
    st.sidebar.markdown("Configure parameters for forecast.")
    
    user_inputs = {}
    with st.sidebar.container():
        for feature in feature_names:
            label = feature.replace('_', ' ').title()
            user_inputs[feature] = st.number_input(f"{label}", value=0.0)

    # --- Tabs ---
    tab1, tab2, tab3, tab4, tab5 = st.tabs(["📊 Performance", "📖 Model Details", "🎯 Stock Forecasting", "📂 Data Preview", "⚠️ Drift Analysis"])

    # --- Tab 1: Performance ---
    with tab1:
        st.subheader("Model Accuracy Insights")
        m1, m2, m3 = st.columns(3)
        m1.metric("Training R² Score", f"{history['train_r2']:.4f}")
        m2.metric("Test R² Score", f"{history['test_r2']:.4f}")
        m3.metric("Test RMSE", f"{history['test_rmse']:.2f}")

        # Actual vs Predicted Plot
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

        # Inventory Risk Matrix
        st.subheader("Inventory Risk Matrix")
        if 'stok_saat_ini' in df.columns:
            fig_risk = px.scatter(df, x='stok_saat_ini', y='predicted_required_stock', 
                                  color='predicted_required_stock', size='predicted_required_stock',
                                  title="Current Stock vs Predicted Need (Risk Matrix)",
                                  labels={'stok_saat_ini': 'Current Stock', 'predicted_required_stock': 'Predicted Need'})
            st.plotly_chart(fig_risk, use_container_width=True)

        st.subheader("Feature Correlation Matrix")
        corr = df.corr()
        fig_corr = px.imshow(corr, text_auto=True, aspect="auto", color_continuous_scale='Greens', 
                             title="Feature Correlation Heatmap")
        st.plotly_chart(fig_corr, use_container_width=True)

        st.subheader("Feature Importance")
        imp_df = pd.DataFrame({'Feature': [f.replace('_', ' ').title() for f in feature_names], 'Importance': model.feature_importances_})
        fig = px.bar(imp_df.sort_values('Importance'), x='Importance', y='Feature', orientation='h', color='Importance', color_continuous_scale='Greens')
        st.plotly_chart(fig, use_container_width=True)

    # --- Tab 2: Model Details ---
    with tab2:
        st.subheader("Model Configuration & Accuracy")
        
        col_d1, col_d2 = st.columns(2)
        with col_d1:
            st.markdown("### Algorithm")
            st.info("Decision Tree Regressor")
            st.markdown("### Methodology")
            st.success("CARD (Classification and Regression Decision Tree)")
            st.markdown("### Hyperparameters")
            st.write(model.get_params())
            
        with col_d2:
            st.markdown("### Detailed Metrics")
            metrics_df = pd.DataFrame([history]).T.rename(columns={0: 'Value'})
            st.table(metrics_df)

    # --- Tab 3: Forecasting ---
    with tab3:
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

    # --- Tab 4: Data Preview ---
    with tab4:
        st.subheader("Sample Training Data")
        data_path = Path("dataset/prepared_data.csv")
        if data_path.exists():
            df_sample = pd.read_csv(data_path).head(100)
            st.dataframe(df_sample, use_container_width=True)
            st.markdown(f"*Showing first 100 rows from `dataset/prepared_data.csv`*")
        else:
            st.warning("Training data not found.")

    # --- Tab 5: Drift Analysis ---
    with tab5:
        st.subheader("Simulate Data Drift (Stress Test)")
        st.markdown("Test how the model responds to abnormal conditions (e.g., sudden sales spike).")
        if st.button("Run Drift Simulation"):
            import simulate_drift
            simulate_drift.simulate_drift("model/stock_prediction_model.pkl", 
                                          "dataset/prepared_data.csv", 
                                          "dataset/drifted_data.csv")
            
        drift_path = Path("dataset/drifted_data.csv")
        if drift_path.exists():
            df_drift = pd.read_csv(drift_path)
            st.write("### Simulation Results: Demand Shock Scenario")
            st.dataframe(df_drift.head(20), use_container_width=True)
            
            # Compare original vs drift prediction
            fig = px.scatter(df_drift, x='predicted_required_stock', y='predicted_drift',
                             title="Original Prediction vs Drifted Prediction",
                             labels={'predicted_required_stock': 'Original', 'predicted_drift': 'Drifted'})
            st.plotly_chart(fig, use_container_width=True)
            
else:
    st.warning("⚠️ Model not found. Run `python app.py --full`.")
