import pandas as pd
import numpy as np
import pickle
from pathlib import Path

def simulate_drift(model_path, data_path, output_path):
    """
    Simulate data drift by generating abnormal pharmacy conditions.
    1. Massive sales spike (Demand shock)
    2. Zero inventory scenario (Stockout)
    """
    print(f"🔄 Simulating Data Drift...")
    
    # Load original data to get feature names and baseline
    df = pd.read_csv(data_path)
    feature_names = [c for c in df.columns if c != 'predicted_required_stock']
    
    # Load model to predict on drift data
    with open(model_path, 'rb') as f:
        model_data = pickle.load(f)
        model = model_data['model']
        trained_features = model_data['feature_names']
    
    # Scenario 1: Demand Shock (Sales spike, high volatility)
    drift_df = df.copy()
    drift_df['total_keluar'] = drift_df['total_keluar'] * 5 
    drift_df['demand_volatility'] = drift_df['demand_volatility'] * 3
    drift_df['stok_saat_ini'] = drift_df['stok_saat_ini'] * 0.2
    
    # Predict using only the features the model was trained on
    X_drift = drift_df[trained_features]
    drift_df['predicted_drift'] = model.predict(X_drift)
    
    # Save drift results
    drift_df.to_csv(output_path, index=False)
    print(f"✅ Drift simulation saved to {output_path}")
    print(f"   Drifted scenarios: {len(drift_df)}")

if __name__ == "__main__":
    simulate_drift("model/stock_prediction_model.pkl", 
                   "dataset/prepared_data.csv", 
                   "dataset/drifted_data.csv")
