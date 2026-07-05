# -*- coding: utf-8 -*-
"""
Stock Prediction Model - Data Preparation Script
Prepares historical data for Decision Tree Regressor training

This script reads the exported CSV file and performs all necessary
data preparation, feature engineering, and validation.
"""

import pandas as pd
import numpy as np
from datetime import datetime, timedelta
import warnings

warnings.filterwarnings('ignore')


class StockPredictionDataPrep:
    """Data preparation class for stock prediction model"""

    def __init__(self, csv_file_path):
        """Initialize with CSV file path"""
        self.csv_file = csv_file_path
        self.df = None
        self.X = None
        self.y = None

    def load_data(self):
        """Load CSV data"""
        print(" Loading CSV data...")
        try:
            self.df = pd.read_csv(self.csv_file)
            print(f"✅ Loaded {len(self.df)} records from {self.csv_file}")
            print(f"   Shape: {self.df.shape}")
            return self.df
        except FileNotFoundError:
            print(f"❌ File not found: {self.csv_file}")
            raise

    def explore_data(self):
        """Perform exploratory data analysis"""
        print("\n Exploratory Data Analysis")
        print("=" * 60)

        print("\n1. Data Shape & Info:")
        print(f"   Rows: {self.df.shape[0]}, Columns: {self.df.shape[1]}")

        print("\n2. Data Types:")
        print(self.df.dtypes)

        print("\n3. Missing Values:")
        missing = self.df.isnull().sum()
        if missing.sum() > 0:
            print(missing[missing > 0])
        else:
            print("   ✅ No missing values")

        print("\n4. Statistical Summary:")
        print(self.df.describe())

        print("\n5. First Few Records:")
        print(self.df.head())

    def clean_data(self):
        """Handle missing values, duplicates, and outliers"""
        print("\n Data Cleaning")
        print("=" * 60)

        # Remove duplicates
        initial_count = len(self.df)
        self.df = self.df.drop_duplicates()
        print(f"✅ Removed duplicates: {initial_count - len(self.df)} rows")

        # Handle missing values
        print("\n✅ Handling missing values...")
        for col in self.df.columns:
            if self.df[col].dtype in ['float64', 'int64']:
                if self.df[col].isnull().sum() > 0:
                    self.df[col].fillna(self.df[col].median(), inplace=True)
            else:
                if self.df[col].isnull().sum() > 0:
                    self.df[col].fillna(self.df[col].mode()[0], inplace=True)

        # Remove obvious outliers (keep data within 99th percentile)
        numeric_cols = self.df.select_dtypes(include=[np.number]).columns
        for col in numeric_cols:
            Q1 = self.df[col].quantile(0.25)
            Q3 = self.df[col].quantile(0.75)
            IQR = Q3 - Q1
            lower_bound = Q1 - 3 * IQR
            upper_bound = Q3 + 3 * IQR

            mask = (self.df[col] >= lower_bound) & (self.df[col] <= upper_bound)
            removed = len(self.df) - len(self.df[mask])
            if removed > 0:
                print(f"   Removed {removed} outliers from {col}")
            self.df = self.df[mask]

        print(f"\n✅ Data cleaned. Final shape: {self.df.shape}")

    def validate_data(self):
        """Validate data consistency"""
        print("\n Data Validation")
        print("=" * 60)

        # Check for negative values in quantities
        quantity_cols = ['total_masuk', 'total_keluar', 'stok_saat_ini', 'avg_daily_sales']
        for col in quantity_cols:
            if col in self.df.columns:
                negative_count = (self.df[col] < 0).sum()
                if negative_count > 0:
                    print(f"⚠️  {negative_count} negative values in {col}")
                    self.df.loc[self.df[col] < 0, col] = 0

        # Validate safety stock is positive
        if 'safety_stock' in self.df.columns:
            self.df['safety_stock'] = self.df['safety_stock'].clip(lower=0)

        # Validate target variable
        if 'predicted_required_stock' in self.df.columns:
            print(f"\n Target variable (predicted_required_stock):")
            print(f"   Min: {self.df['predicted_required_stock'].min():.2f}")
            print(f"   Max: {self.df['predicted_required_stock'].max():.2f}")
            print(f"   Mean: {self.df['predicted_required_stock'].mean():.2f}")
            print(f"   Std: {self.df['predicted_required_stock'].std():.2f}")

        print("\n✅ Data validation complete")

    def prepare_features(self):
        """Prepare features for modeling"""
        print("\n Feature Preparation")
        print("=" * 60)

        # 8 Fitur utama yang konsisten antara database Laravel dan model ML
        # Urutan ini HARUS sama dengan yang dikirim ke Flask API
        feature_cols = [
            'total_masuk',          # Total stok masuk historis
            'total_keluar',         # Total stok keluar historis
            'stok_saat_ini',        # Stok saat ini
            'frekuensi_transaksi',  # Frekuensi transaksi 3 bulan (data variasi)
            'avg_transaksi',        # Rata-rata qty per transaksi
            'demand_volatility',    # Volatilitas permintaan harian
            'avg_daily_sales',      # Rata-rata penjualan harian (3 bulan)
            'safety_stock',         # Safety stock threshold
        ]

        # Hanya pakai kolom yang benar-benar ada di dataframe
        available_features = [col for col in feature_cols if col in self.df.columns]
        missing = [col for col in feature_cols if col not in self.df.columns]

        if missing:
            print(f"\n⚠️  Kolom tidak ditemukan di CSV (akan diabaikan): {missing}")

        self.X = self.df[available_features].copy()
        self.y = self.df['predicted_required_stock'].copy()

        print(f"✅ Features prepared: {self.X.shape[1]} features, {self.X.shape[0]} samples")
        print(f"\n   Feature columns:")
        for i, col in enumerate(self.X.columns, 1):
            print(f"   {i:2d}. {col}")

        print(f"\n   Target variable: predicted_required_stock")
        print(f"   Shape: {self.y.shape}")

        return self.X, self.y

    def get_feature_statistics(self):
        """Display feature statistics"""
        print("\n Feature Statistics")
        print("=" * 60)

        for col in self.X.columns:
            print(f"\n{col}:")
            if pd.api.types.is_numeric_dtype(self.X[col]):
                print(f"   Min: {self.X[col].min():.4f}")
                print(f"   Max: {self.X[col].max():.4f}")
                print(f"   Mean: {self.X[col].mean():.4f}")
                print(f"   Std: {self.X[col].std():.4f}")
            else:
                counts = self.X[col].value_counts()
                print(f"   Type: Categorical")
                print(f"   Unique Values: {len(counts)}")
                print(f"   Top Value: '{counts.index[0]}' ({counts.iloc[0]} occurrences)")

    def export_prepared_data(self, output_path):
        """Export prepared data to CSV"""
        print(f"\n Exporting prepared data to {output_path}")

        prepared_df = pd.concat([self.X, self.y], axis=1)
        prepared_df.to_csv(output_path, index=False)

        print(f"✅ Prepared data saved: {output_path}")
        print(f"   Records: {len(prepared_df)}")

    def run_pipeline(self, output_path=None):
        """Run complete data preparation pipeline"""
        print("\n" + "=" * 60)
        print("STOCK PREDICTION DATA PREPARATION PIPELINE")
        print("=" * 60)

        self.load_data()
        self.explore_data()
        self.clean_data()
        self.validate_data()
        self.prepare_features()
        self.get_feature_statistics()

        if output_path:
            self.export_prepared_data(output_path)

        print("\n" + "=" * 60)
        print("✅ DATA PREPARATION COMPLETE")
        print("=" * 60)

        return self.X, self.y


# Example usage
if __name__ == "__main__":
    # Initialize data preparation
    prep = StockPredictionDataPrep("dataset/training_data.csv")

    # Run pipeline
    X, y = prep.run_pipeline(output_path="dataset/prepared_data.csv")
