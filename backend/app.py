#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Stock Prediction Model - Main Application
Integrates data preparation, model training, and API deployment

Usage:
    python app.py --export      # Export data from Laravel
    python app.py --prepare     # Only prepare data
    python app.py --train       # Only train model
    python app.py --api         # Only start API
    python app.py --full        # Full pipeline (export + prepare + train)
    python app.py --demo        # Demo prediction
"""

import os
import sys
import argparse
import subprocess
import pickle
from pathlib import Path

# Fix Unicode encoding on Windows
if sys.platform == 'win32':
    import io
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

# Add parent directory to path
sys.path.insert(0, str(Path(__file__).parent))

from dataset.data_preparation import StockPredictionDataPrep
from notebook.model_trainer import StockPredictionModel


class StockPredictionPipeline:
    """Complete pipeline for stock prediction model"""

    def __init__(self, config=None):
        """Initialize pipeline with configuration"""
        self.config = config or {}
        self.base_dir = Path(__file__).parent
        self.root_dir = self.base_dir.parent
        self.frontend_dir = self.root_dir / 'frontend'

        # Set paths
        self.dataset_dir = self.base_dir / 'dataset'
        self.model_dir = self.base_dir / 'model'
        self.api_dir = self.base_dir / 'api'

        # Ensure directories exist
        self.dataset_dir.mkdir(exist_ok=True)
        self.model_dir.mkdir(exist_ok=True)
        self.api_dir.mkdir(exist_ok=True)

        print("\n" + "=" * 70)
        print("STOCK PREDICTION MODEL - MAIN APP")
        print("=" * 70)

    def step0_export_data(self, months=12):
        """Step 0: Export data from Laravel"""
        print("\n" + "-" * 70)
        print("STEP 0: EXPORT DATA FROM LARAVEL")
        print("-" * 70)

        if not self.frontend_dir.exists():
            print(f"❌ Frontend directory not found: {self.frontend_dir}")
            return False

        # 1. Run Artisan command
        print(f"🚀 Running Laravel export command (months={months})...")
        try:
            # Determine artisan path
            artisan_path = self.frontend_dir / 'artisan'
            if not artisan_path.exists():
                print(f"❌ artisan file not found in {self.frontend_dir}")
                return False

            result = subprocess.run(
                ['php', 'artisan', 'prediksi:export-data', f'--months={months}'],
                cwd=str(self.frontend_dir),
                capture_output=True,
                text=True,
                encoding='utf-8'
            )

            if result.returncode != 0:
                print(f"❌ Artisan command failed:\n{result.stderr}")
                return False

            print("✅ Artisan command executed successfully")
            
            # 2. Find the latest exported file in frontend/storage/training_data_prediksi_stok
            frontend_storage = self.frontend_dir / 'storage' / 'training_data_prediksi_stok'
            if not frontend_storage.exists():
                print(f"❌ Export folder not found: {frontend_storage}")
                return False

            export_files = list(frontend_storage.glob('training_data_*.csv'))
            if not export_files:
                print(f"❌ No export files found in {frontend_storage}")
                return False

            latest_export = max(export_files, key=os.path.getctime)
            print(f"📂 Found latest export: {latest_export.name}")

            # 3. Copy to backend dataset folder
            import shutil
            target_path = self.dataset_dir / f"training_data_latest.csv"
            shutil.copy2(latest_export, target_path)
            
            print(f"✅ Copied to: {target_path}")
            return True

        except Exception as e:
            print(f"❌ Error during export: {str(e)}")
            return False

    def step1_prepare_data(self):
        """Step 1: Prepare training data"""
        print("\n" + "-" * 70)
        print("STEP 1: DATA PREPARATION")
        print("-" * 70)

        # Find latest training data CSV
        # Prioritize 'training_data_latest.csv' if it exists, otherwise look for glob
        target_csv = self.dataset_dir / 'training_data_latest.csv'
        
        if target_csv.exists():
            latest_csv = target_csv
        else:
            csv_files = list(self.dataset_dir.glob('training_data_*.csv'))
            if not csv_files:
                print("❌ No training data found in dataset/ directory")
                print("   Please run: python app.py --export")
                return False
            latest_csv = max(csv_files, key=os.path.getctime)

        print(f"\n📂 Using training data: {latest_csv.name}")

        # Prepare data
        prep = StockPredictionDataPrep(str(latest_csv))
        X, y = prep.run_pipeline(output_path=str(self.dataset_dir / 'prepared_data.csv'))

        print(f"\n✅ Data preparation complete")
        print(f"   Features: {X.shape[1]}")
        print(f"   Samples: {len(X)}")

        return True

    def step2_train_model(self):
        """Step 2: Train prediction model"""
        print("\n" + "-" * 70)
        print("STEP 2: MODEL TRAINING")
        print("-" * 70)

        # Check if prepared data exists
        prepared_data = self.dataset_dir / 'prepared_data.csv'
        if not prepared_data.exists():
            print(f"❌ Prepared data not found: {prepared_data}")
            print("   Please run data preparation first")
            return False

        # Train model
        trainer = StockPredictionModel()
        model = trainer.run_training_pipeline(
            data_path=str(prepared_data),
            model_path=str(self.model_dir / 'stock_prediction_model.pkl')
        )

        print(f"\n✅ Model training complete")
        print(f"   Model saved to: {self.model_dir / 'stock_prediction_model.pkl'}")

        return True

    def step3_start_api(self):
        """Step 3: Start Flask API"""
        print("\n" + "-" * 70)
        print("STEP 3: API DEPLOYMENT")
        print("-" * 70)

        # Check if model exists
        model_file = self.model_dir / 'stock_prediction_model.pkl'
        if not model_file.exists():
            print(f"❌ Model file not found: {model_file}")
            print("   Please train the model first")
            return False

        print(f"\n🚀 Starting Flask API...")
        print(f"   Model: {model_file}")
        print(f"   Port: 5000")
        print(f"\n📍 API Endpoints:")
        print(f"   GET  http://localhost:5000/status")
        print(f"   GET  http://localhost:5000/model/info")
        print(f"   POST http://localhost:5000/predict")
        print(f"   POST http://localhost:5000/predict/batch")

        # Start API
        try:
            subprocess.run([
                sys.executable,
                str(self.api_dir / 'app.py'),
                str(model_file)
            ])
        except KeyboardInterrupt:
            print("\n\n✅ API stopped gracefully")

        return True

    def step4_demo_prediction(self):
        """Step 4: Demo prediction"""
        print("\n" + "-" * 70)
        print("STEP 4: DEMO PREDICTION")
        print("-" * 70)

        # Check if model exists
        model_file = self.model_dir / 'stock_prediction_model.pkl'
        if not model_file.exists():
            print(f"❌ Model file not found: {model_file}")
            return False

        # Load model
        print(f"\n📂 Loading model: {model_file}")
        with open(model_file, 'rb') as f:
            model_dict = pickle.load(f)

        model = model_dict['model']
        feature_names = model_dict['feature_names']

        # Sample input
        sample_input = {
            'total_masuk': 500,
            'total_keluar': 150,
            'stok_saat_ini': 350,
            'frekuensi_transaksi': 15,
            'avg_transaksi': 10.0,
            'kedaluwarsa_risk': 2,
            'accuracy_deviation': 5.5,
            'safety_stock_ratio': 3.5,
            'demand_volatility': 12.3,
            'trend_indicator': 1.05,
            'safety_stock': 100,
            'avg_daily_sales': 5.0
        }

        # Prepare input
        import numpy as np
        X = np.array([sample_input[f] for f in feature_names]).reshape(1, -1)

        # Predict
        prediction = model.predict(X)[0]

        print(f"\n📊 Sample Input:")
        for feature, value in sample_input.items():
            print(f"   {feature:30s} = {value}")

        print(f"\n🎯 Prediction Result:")
        print(f"   Predicted Required Stock = {prediction:.2f} units")
        print(f"   Current Stock            = {sample_input['stok_saat_ini']:.0f} units")

        gap = prediction - sample_input['stok_saat_ini']
        if gap > 0:
            print(f"   Stock Gap (need to order) = {gap:.2f} units")
        else:
            print(f"   Excess Stock             = {abs(gap):.2f} units")

        print(f"\n✅ Demo prediction complete")
        return True

    def run_full_pipeline(self, months=12):
        """Run full pipeline (export + prepare + train)"""
        print("\n🔄 Running full pipeline...")

        if not self.step0_export_data(months=months):
            print("⚠️ Data export failed, attempting to use existing data...")
            # Continue anyway, step1 will check for existing data

        if not self.step1_prepare_data():
            return False

        if not self.step2_train_model():
            return False

        print("\n" + "=" * 70)
        print("✅ PIPELINE COMPLETE")
        print("=" * 70)
        print("\n📌 Next steps:")
        print("   1. Start API: python app.py --api")
        print("   2. Test prediction: python app.py --demo")
        print("   3. Integrate with Laravel: See backend/README.md")

        return True


def main():
    """Main entry point"""
    parser = argparse.ArgumentParser(
        description='Stock Prediction Model - Complete Pipeline'
    )
    parser.add_argument('--export', action='store_true', help='Only export data from Laravel')
    parser.add_argument('--prepare', action='store_true', help='Only prepare data')
    parser.add_argument('--train', action='store_true', help='Only train model')
    parser.add_argument('--api', action='store_true', help='Only start API')
    parser.add_argument('--full', action='store_true', help='Full pipeline (export + prepare + train)')
    parser.add_argument('--demo', action='store_true', help='Demo prediction')
    parser.add_argument('--months', type=int, default=12, help='Months of data to export (default: 12)')

    args = parser.parse_args()

    # Initialize pipeline
    pipeline = StockPredictionPipeline()

    # Run based on arguments
    try:
        success = False
        if args.export:
            success = pipeline.step0_export_data(months=args.months)
        elif args.prepare:
            success = pipeline.step1_prepare_data()
        elif args.train:
            success = pipeline.step2_train_model()
        elif args.api:
            success = pipeline.step3_start_api()
        elif args.demo:
            success = pipeline.step4_demo_prediction()
        elif args.full:
            success = pipeline.run_full_pipeline(months=args.months)
        else:
            # Default: show help
            parser.print_help()
            return 0

        return 0 if success else 1

    except Exception as e:
        print(f"\n❌ Error: {str(e)}")
        import traceback
        traceback.print_exc()
        return 1


if __name__ == '__main__':
    sys.exit(main())
