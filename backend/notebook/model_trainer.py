# -*- coding: utf-8 -*-
"""
Stock Prediction Model - Decision Tree Regressor Trainer
Implements the CARD method for regression on pharmaceutical stock data

This module:
1. Loads prepared data
2. Performs feature selection
3. Trains Decision Tree Regressor
4. Evaluates model performance
5. Optimizes hyperparameters
6. Saves trained model
"""

import pandas as pd
import numpy as np
import pickle
import json
from datetime import datetime
from pathlib import Path

from sklearn.model_selection import train_test_split, GridSearchCV, cross_val_score
from sklearn.tree import DecisionTreeRegressor
from sklearn.preprocessing import StandardScaler
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
import matplotlib.pyplot as plt
import seaborn as sns

import warnings
warnings.filterwarnings('ignore')


class StockPredictionModel:
    """Decision Tree Regressor for stock quantity prediction using CARD method"""

    def __init__(self):
        """Initialize model components"""
        self.model = None
        self.scaler = None
        self.X_train = None
        self.X_test = None
        self.y_train = None
        self.y_test = None
        self.feature_names = None
        self.training_history = {}

    def load_data(self, csv_path):
        """Load prepared data from CSV"""
        print("Loading data...")
        df = pd.read_csv(csv_path)

        # Kolom fitur numerik yang digunakan model (8 fitur)
        # Kolom id_obat dan nama_obat di-drop agar tidak jadi fitur
        feature_cols = [
            'total_masuk', 'total_keluar', 'stok_saat_ini',
            'frekuensi_transaksi', 'avg_transaksi',
            'demand_volatility', 'avg_daily_sales', 'safety_stock'
        ]
        target_col = 'predicted_required_stock'

        # Validasi kolom ada di CSV
        available_features = [c for c in feature_cols if c in df.columns]
        missing = [c for c in feature_cols if c not in df.columns]
        if missing:
            print(f"[WARNING] Kolom tidak ditemukan di CSV: {missing}")

        # Separate features and target
        self.X = df[available_features].copy()
        self.y = df[target_col].copy()
        self.feature_names = self.X.columns.tolist()

        print(f"Data loaded:")
        print(f"   Rows: {len(df)}, Features: {len(self.feature_names)}")
        print(f"   Features: {self.feature_names}")
        print(f"   Target: {target_col}")

        return self.X, self.y

    def analyze_correlations(self):
        """Analyze feature correlations with target"""
        print("\n📊 Feature Correlation Analysis")
        print("=" * 60)

        # Create correlation matrix
        data_with_target = self.X.copy()
        data_with_target['predicted_required_stock'] = self.y

        # Only use numeric columns for correlation
        numeric_data = data_with_target.select_dtypes(include=[np.number])
        correlations = numeric_data.corr()['predicted_required_stock'].drop('predicted_required_stock')
        
        # Fill NaN correlations (zero variance features) with 0
        correlations = correlations.fillna(0)
        correlations_sorted = correlations.abs().sort_values(ascending=False)

        print("\n🎯 Feature Correlations with Target (sorted by strength):")
        for i, (feature, corr_val) in enumerate(correlations_sorted.items(), 1):
            strength = "⭐" * min(5, int(abs(corr_val) * 5 + 1))
            print(f"   {i:2d}. {feature:30s} : {correlations[feature]:7.4f} {strength}")

        # Identify highly correlated features
        print("\n🔗 Highly Correlated Features (|r| > 0.9):")
        high_corr_pairs = []
        for i in range(len(correlations)):
            for j in range(i + 1, len(correlations)):
                if abs(correlations.iloc[i] - correlations.iloc[j]) > 0.9:
                    high_corr_pairs.append(
                        (self.feature_names[i], self.feature_names[j])
                    )

        if high_corr_pairs:
            for feat1, feat2 in high_corr_pairs:
                print(f"   • {feat1} ↔ {feat2}")
        else:
            print("   ✅ No highly correlated feature pairs")

        return correlations

    def feature_selection(self, min_correlation=0.0):
        """
        Select features for training.

        CATATAN PENTING: Jangan hapus zero-variance features!
        Meskipun di data training banyak nilai 0 (karena obat belum ada transaksi),
        fitur-fitur seperti frekuensi_transaksi dan avg_daily_sales TETAP PENTING
        saat production dengan data transaksi nyata.

        Model harus dilatih dengan SEMUA 8 fitur agar bisa menerima input lengkap
        dari Laravel saat production.
        """
        print(f"\n🔍 Feature Selection (mode: keep all 8 features for production)")
        print("=" * 60)

        # Analisis korelasi untuk informasi saja
        correlations = self.analyze_correlations()

        # TIDAK menghapus zero-variance features
        # Alasan: fitur seperti frekuensi_transaksi, avg_daily_sales bernilai 0
        # di training karena data kurang, tapi AKAN berisi nilai nyata di production.
        # Menghapusnya akan membuat model tidak bisa menerima data production.
        zero_variance = [col for col in self.X.columns if self.X[col].std() == 0]
        if zero_variance:
            print(f"\nℹ️  Zero-variance features (TIDAK dihapus, tetap digunakan): {zero_variance}")
            print(f"   Alasan: Fitur ini akan berisi nilai nyata saat production")
            print(f"   dengan lebih banyak data transaksi obat keluar.")

        print(f"\n✅ Using {len(self.feature_names)} features: {self.feature_names}")
        return self.X

    def split_data(self, test_size=0.2, random_state=42):
        """Split data into train and test sets with temporal awareness"""
        print(f"\n✂️  Train-Test Split (80-20)")
        print("=" * 60)

        # For time-series data, we preserve temporal order
        split_idx = int(len(self.X) * (1 - test_size))

        self.X_train = self.X.iloc[:split_idx]
        self.X_test = self.X.iloc[split_idx:]
        self.y_train = self.y.iloc[:split_idx]
        self.y_test = self.y.iloc[split_idx:]

        print(f"\n✅ Data split completed:")
        print(f"   Training set: {len(self.X_train)} samples ({len(self.X_train)/len(self.X)*100:.1f}%)")
        print(f"   Test set: {len(self.X_test)} samples ({len(self.X_test)/len(self.X)*100:.1f}%)")

    def build_initial_model(self):
        """Build initial Decision Tree Regressor with default parameters"""
        print("\n🌳 Building Initial Decision Tree Regressor (CARD Method)")
        print("=" * 60)

        # CARD Method Parameters
        # Optimized for stock prediction
        self.model = DecisionTreeRegressor(
            max_depth=10,
            min_samples_split=5,
            min_samples_leaf=2,
            random_state=42,
            criterion='squared_error'  # MSE-based splitting for regression
        )

        print("\n🔧 Initial Model Parameters (CARD-optimized):")
        print(f"   max_depth: 10")
        print(f"   min_samples_split: 5")
        print(f"   min_samples_leaf: 2")
        print(f"   criterion: squared_error (MSE)")

        print("\n📚 Training model on training set...")
        self.model.fit(self.X_train, self.y_train)

        # Calculate training accuracy
        train_score = self.model.score(self.X_train, self.y_train)
        test_score = self.model.score(self.X_test, self.y_test)

        print(f"\n✅ Model trained:")
        print(f"   Training R² Score: {train_score:.4f}")
        print(f"   Test R² Score: {test_score:.4f}")

    def evaluate_model(self):
        """Evaluate model with multiple metrics"""
        print("\n📊 Model Evaluation Metrics")
        print("=" * 60)

        # Training metrics
        y_train_pred = self.model.predict(self.X_train)
        train_mae = mean_absolute_error(self.y_train, y_train_pred)
        train_mse = mean_squared_error(self.y_train, y_train_pred)
        train_rmse = np.sqrt(train_mse)
        train_r2 = r2_score(self.y_train, y_train_pred)

        # Test metrics
        y_test_pred = self.model.predict(self.X_test)
        test_mae = mean_absolute_error(self.y_test, y_test_pred)
        test_mse = mean_squared_error(self.y_test, y_test_pred)
        test_rmse = np.sqrt(test_mse)
        test_r2 = r2_score(self.y_test, y_test_pred)

        print("\n📈 TRAINING SET METRICS:")
        print(f"   MAE (Mean Absolute Error):  {train_mae:.4f} units")
        print(f"   MSE (Mean Squared Error):   {train_mse:.4f}")
        print(f"   RMSE (Root Mean Squared):   {train_rmse:.4f} units")
        print(f"   R² Score:                   {train_r2:.4f}")

        print("\n🧪 TEST SET METRICS:")
        print(f"   MAE (Mean Absolute Error):  {test_mae:.4f} units")
        print(f"   MSE (Mean Squared Error):   {test_mse:.4f}")
        print(f"   RMSE (Root Mean Squared):   {test_rmse:.4f} units")
        print(f"   R² Score:                   {test_r2:.4f}")

        # Interpretation
        print("\n💡 Interpretation:")
        if test_r2 > 0.7:
            print("   ✅ Excellent model performance (R² > 0.7)")
        elif test_r2 > 0.5:
            print("   ✓  Good model performance (R² > 0.5)")
        else:
            print("   ⚠️  Model needs improvement (R² <= 0.5)")

        # Store metrics
        self.training_history = {
            'train_mae': float(train_mae),
            'train_rmse': float(train_rmse),
            'train_r2': float(train_r2),
            'test_mae': float(test_mae),
            'test_rmse': float(test_rmse),
            'test_r2': float(test_r2),
            'samples_train': len(self.y_train),
            'samples_test': len(self.y_test)
        }

        return {
            'train': {'mae': train_mae, 'rmse': train_rmse, 'r2': train_r2},
            'test': {'mae': test_mae, 'rmse': test_rmse, 'r2': test_r2}
        }

    def hyperparameter_tuning(self):
        """Optimize hyperparameters using GridSearchCV"""
        print("\n🔍 Hyperparameter Tuning (GridSearchCV)")
        print("=" * 60)

        param_grid = {
            'max_depth': [8, 10, 12, 15],
            'min_samples_split': [3, 5, 7],
            'min_samples_leaf': [1, 2, 3],
        }

        print("\nTesting parameter combinations...")
        print(f"Total combinations: {np.prod([len(v) for v in param_grid.values()])}")

        # GridSearchCV
        grid_search = GridSearchCV(
            DecisionTreeRegressor(random_state=42, criterion='squared_error'),
            param_grid,
            cv=5,
            scoring='r2',
            n_jobs=-1,
            verbose=1
        )

        grid_search.fit(self.X_train, self.y_train)

        print(f"\n✅ Best parameters found:")
        for param, value in grid_search.best_params_.items():
            print(f"   {param}: {value}")

        print(f"\n   Best CV Score (R²): {grid_search.best_score_:.4f}")

        # Update model
        self.model = grid_search.best_estimator_

        # Evaluate tuned model
        y_test_pred = self.model.predict(self.X_test)
        tuned_r2 = r2_score(self.y_test, y_test_pred)
        print(f"   Test R² after tuning: {tuned_r2:.4f}")

    def feature_importance(self):
        """Display feature importance"""
        print("\n📊 Feature Importance")
        print("=" * 60)

        importances = self.model.feature_importances_
        feature_importance_df = pd.DataFrame({
            'feature': self.feature_names,
            'importance': importances
        }).sort_values('importance', ascending=False)

        print("\n🎯 Top Features:")
        for idx, row in feature_importance_df.head(10).iterrows():
            bar = "█" * int(row['importance'] * 100)
            print(f"   {row['feature']:30s} | {bar} {row['importance']:.4f}")

        return feature_importance_df

    def save_model(self, model_path):
        """Save trained model to pickle file"""
        print(f"\n💾 Saving Model")
        print("=" * 60)

        # Ensure directory exists
        Path(model_path).parent.mkdir(parents=True, exist_ok=True)

        import sklearn as _sklearn
        # Save model
        model_dict = {
            'model': self.model,
            'feature_names': self.feature_names,
            'training_history': self.training_history,
            'trained_date': datetime.now().isoformat(),
            'sklearn_version': _sklearn.__version__,  # PENTING untuk debug mismatch versi
        }

        with open(model_path, 'wb') as f:
            pickle.dump(model_dict, f)

        file_size = Path(model_path).stat().st_size / 1024
        print(f"✅ Model saved: {model_path}")
        print(f"   File size: {file_size:.2f} KB")
        print(f"   Features: {len(self.feature_names)}")
        print(f"   Trained at: {model_dict['trained_date']}")

    def run_training_pipeline(self, data_path, model_path):
        """Run complete training pipeline"""
        print("\n" + "=" * 70)
        print("STOCK PREDICTION MODEL TRAINING PIPELINE - CARD METHOD")
        print("=" * 70)

        # Load data
        self.load_data(data_path)

        # Feature selection
        self.feature_selection(min_correlation=0.05)

        # Train-test split
        self.split_data()

        # Build and train initial model
        self.build_initial_model()

        # Evaluate
        metrics = self.evaluate_model()

        # Feature importance
        self.feature_importance()

        # Hyperparameter tuning
        self.hyperparameter_tuning()

        # Final evaluation
        print("\n📊 Final Model Evaluation After Tuning:")
        self.evaluate_model()

        # Save model
        self.save_model(model_path)

        print("\n" + "=" * 70)
        print("✅ TRAINING PIPELINE COMPLETE")
        print("=" * 70)

        return self.model


# Example usage
if __name__ == "__main__":
    trainer = StockPredictionModel()
    model = trainer.run_training_pipeline(
        data_path="dataset/training_data_from_db.csv",  # Data nyata dari database
        model_path="model/stock_prediction_model.pkl"
    )
