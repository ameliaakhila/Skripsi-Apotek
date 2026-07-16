# -*- coding: utf-8 -*-
"""
Stock Prediction Flask API
Serves the trained Decision Tree Regressor model via REST endpoints

Endpoints:
- GET  /status                    - API health check
- GET  /model/info                - Model information
- POST /predict                   - Predict stock quantity
- POST /predict/batch             - Batch predictions
"""

import pickle
import numpy as np
import pandas as pd
from datetime import datetime
from typing import Dict, List, Union

import matplotlib
matplotlib.use('Agg')  # Use non-GUI backend for matplotlib
import matplotlib.pyplot as plt
from sklearn.tree import plot_tree
from flask import Flask, request, jsonify, send_file
from flask_cors import CORS
import logging

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # Enable CORS for Laravel frontend

# Global model cache
_model = None
_feature_names = None
_training_history = None

# ─────────────────────────────────────────────────────────────
# AUTO-LOAD MODEL saat modul diimport
# (Gunicorn tidak melalui __main__, jadi load harus di sini)
# ─────────────────────────────────────────────────────────────
import os as _os

_DEFAULT_MODEL_PATH = _os.environ.get('MODEL_PATH', 'model/stock_prediction_model.pkl')

def _auto_load_model():
    """Load model otomatis saat aplikasi startup (kompatibel dengan Gunicorn)"""
    global _model, _feature_names, _training_history
    try:
        if _os.path.exists(_DEFAULT_MODEL_PATH):
            with open(_DEFAULT_MODEL_PATH, 'rb') as f:
                model_dict = __import__('pickle').load(f)
            _model = model_dict['model']
            _feature_names = model_dict['feature_names']
            _training_history = model_dict['training_history']
            logger.info(f"✅ Model auto-loaded from: {_DEFAULT_MODEL_PATH}")
        else:
            logger.error(f"❌ Model file not found at startup: {_DEFAULT_MODEL_PATH}")
            logger.error(f"   Current working dir: {_os.getcwd()}")
            logger.error(f"   Files in model/: {_os.listdir('model') if _os.path.exists('model') else 'folder tidak ada'}")
    except Exception as e:
        logger.error(f"❌ Auto-load model failed: {str(e)}")

# Jalankan auto-load saat modul diimport
_auto_load_model()


def load_model(model_path: str):
    """Load trained model from pickle file"""
    global _model, _feature_names, _training_history

    if _model is not None:
        return _model

    logger.info(f"Loading model from {model_path}...")

    try:
        with open(model_path, 'rb') as f:
            model_dict = pickle.load(f)

        _model = model_dict['model']
        _feature_names = model_dict['feature_names']
        _training_history = model_dict['training_history']

        logger.info("✅ Model loaded successfully")
        return _model
    except FileNotFoundError:
        logger.error(f"Model file not found: {model_path}")
        raise
    except Exception as e:
        logger.error(f"Error loading model: {str(e)}")
        raise


def validate_features(data: Dict, required_features: List[str]) -> Union[bool, str]:
    """Validate that all required features are present in data"""
    missing_features = [f for f in required_features if f not in data]

    if missing_features:
        return False, f"Missing features: {', '.join(missing_features)}"

    return True, "OK"


def prepare_input_vector(data: Dict, feature_names: List[str]) -> pd.DataFrame:
    """
    Prepare input as pandas DataFrame with proper column names.
    Fixes sklearn UserWarning: 'X does not have valid feature names'
    Model was trained with DataFrame, so prediction must also use DataFrame.
    """
    return pd.DataFrame(
        [[float(data[f]) for f in feature_names]],
        columns=feature_names
    )


@app.route('/', methods=['GET'])
def home():
    """Root endpoint - API info"""
    return jsonify({
        'name': 'Apotek Stock Prediction API',
        'version': '1.0.0',
        'status': 'running',
        'model_loaded': _model is not None,
        'endpoints': {
            'health':      'GET  /status',
            'model_info':  'GET  /model/info',
            'predict':     'POST /predict',
            'batch':       'POST /predict/batch'
        }
    }), 200


@app.route('/status', methods=['GET'])
def status():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'timestamp': datetime.now().isoformat(),
        'model_loaded': _model is not None
    }), 200


@app.route('/model/info', methods=['GET'])
def model_info():
    """Get model information"""
    if _model is None:
        return jsonify({'error': 'Model not loaded'}), 500

    return jsonify({
        'model_type': 'Decision Tree Regressor',
        'methodology': 'CARD (Classification and Regression Decision Tree)',
        'output_type': 'Numeric (stock quantity)',
        'total features': len(_feature_names),
        'features': _feature_names,
        'training_metrics': _training_history,
        'loaded_at': datetime.now().isoformat()
    }), 200


@app.route('/predict', methods=['POST'])
def predict():
    """
    Predict required stock quantity

    Expected JSON input:
    {
        "total_masuk": 500,
        "total_keluar": 150,
        "stok_saat_ini": 350,
        "frekuensi_transaksi": 15,
        "avg_transaksi": 10.0,
        "kedaluwarsa_risk": 2,
        "accuracy_deviation": 5.5,
        "safety_stock_ratio": 3.5,
        "demand_volatility": 12.3,
        "trend_indicator": 1.05,
        "safety_stock": 100,
        "avg_daily_sales": 5.0
    }

    Returns:
    {
        "predicted_stock": 250.5,
        "confidence": "high",
        "recommendation": "Stock quantity sufficient",
        "timestamp": "2026-04-27T10:30:45.123456"
    }
    """
    if _model is None:
        return jsonify({'error': 'Model not loaded'}), 500

    try:
        # Get JSON data
        data = request.get_json()

        if not data:
            return jsonify({
                'error': 'No JSON data provided',
                'required_features': _feature_names
            }), 400

        # Validate features
        is_valid, message = validate_features(data, _feature_names)
        if not is_valid:
            return jsonify({
                'error': message,
                'required_features': _feature_names,
                'provided_features': list(data.keys())
            }), 400

        # Prepare input
        X = prepare_input_vector(data, _feature_names)

        # Make prediction
        prediction = _model.predict(X)[0]
        confidence = calculate_confidence(prediction, data)
        recommendation = generate_recommendation(prediction, data)
        rekomendasi_order = max(0, prediction - data.get('stok_saat_ini', 0))

        return jsonify({
            'predicted_stock': float(prediction),
            'confidence': confidence,
            'recommendation': recommendation,
            'rekomendasi_order': float(rekomendasi_order),
            'rekomendasi_order_text': recommendation,
            'input_features': {k: v for k, v in data.items()},
            'timestamp': datetime.now().isoformat()
        }), 200

    except ValueError as e:
        logger.error(f"Value error: {str(e)}")
        return jsonify({
            'error': f'Invalid input value: {str(e)}'
        }), 400
    except Exception as e:
        logger.error(f"Prediction error: {str(e)}")
        return jsonify({
            'error': f'Prediction failed: {str(e)}'
        }), 500


@app.route('/predict/batch', methods=['POST'])
def predict_batch():
    """
    Optimized Batch prediction endpoint using matrix operations
    """
    if _model is None:
        return jsonify({'error': 'Model not loaded'}), 500

    try:
        data = request.get_json()
        if not data or 'predictions' not in data:
            return jsonify({'error': 'Missing "predictions" key'}), 400

        predictions_list = data['predictions']
        if not predictions_list:
            return jsonify({'results': [], 'count': 0}), 200

        # 1. Prepare data matrix for vectorized prediction
        X_list = []
        valid_indices = []
        results = [None] * len(predictions_list)

        for i, item in enumerate(predictions_list):
            is_valid, _ = validate_features(item, _feature_names)
            if is_valid:
                X_list.append([float(item[f]) for f in _feature_names])
                valid_indices.append(i)
            else:
                results[i] = {
                    'id_obat': item.get('id_obat'),
                    'error': 'Invalid or missing features'
                }

        # 2. Perform batch prediction dengan pandas DataFrame (fix sklearn warning)
        if X_list:
            # Gunakan DataFrame agar nama fitur dipertahankan → tidak ada UserWarning
            X_matrix = pd.DataFrame(X_list, columns=_feature_names)
            batch_predictions = _model.predict(X_matrix)
            
            # 3. Process results
            for i, idx in enumerate(valid_indices):
                prediction = float(batch_predictions[i])
                item = predictions_list[idx]
                stok_saat_ini = item.get('stok_saat_ini', 0)
                rekomendasi_order = max(0, prediction - stok_saat_ini)
                results[idx] = {
                    'id_obat': item.get('id_obat'),
                    'predicted_stock': prediction,
                    'confidence': calculate_confidence(prediction, item),
                    'rekomendasi_order': float(rekomendasi_order),
                    'rekomendasi_order_text': generate_recommendation(prediction, item)
                }

        return jsonify({
            'results': results,
            'count': len(results),
            'timestamp': datetime.now().isoformat()
        }), 200

    except Exception as e:
        logger.error(f"Batch prediction error: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/model/tree-image', methods=['GET'])
def get_tree_image():
    """Endpoint menghasilkan gambar struktur root node decision tree"""
    if _model is None:
        return jsonify({'error': 'Model not loaded'}), 500
    
    try:
        # inisialisasi ukuran canvas untuk gambar
        fig, ax = plt.subplots(figsize=(12, 6))

        # gambar decision tree (dibatasi level max_depth=2 untuk menghindari terlalu banyak node)
        plot_tree(_model,
                    max_depth=2,
                    feature_names=_feature_names,
                    filled=True,
                    rounded=True,
                    prescision=2,
                    ax=ax
                )
        
        image_path = 'model/tree_structure.png'
        plt.savefig(image_path, bbox_inches='tight', dpi=150)
        plt.close(fig)  # tutup figure untuk menghemat memori

        return send_file(image_path, mimetype='image/png')

    except Exception as e:
        logger.error(f"Error generating tree image: {str(e)}")
        return jsonify({'error': f'Failed to generate tree image: {str(e)}'}), 500


def calculate_confidence(prediction: float, features: Dict) -> str:
    """Calculate prediction confidence based on input features"""
    # Simple confidence heuristic
    stok_saat_ini = features.get('stok_saat_ini', 0)
    safety_stock = features.get('safety_stock', 1)
    frekuensi = features.get('frekuensi_transaksi', 1)

    # Factors affecting confidence
    confidence_score = 0.5

    # If stock is well above safety stock, higher confidence
    if stok_saat_ini >= safety_stock * 1.5:
        confidence_score += 0.2
    elif stok_saat_ini < safety_stock:
        confidence_score -= 0.1

    # If transaction frequency is high, more data = higher confidence
    if frekuensi > 20:
        confidence_score += 0.2
    elif frekuensi < 5:
        confidence_score -= 0.1

    # Volatility affects confidence
    volatility = features.get('demand_volatility', 0)
    if volatility < 10:
        confidence_score += 0.1
    elif volatility > 50:
        confidence_score -= 0.2

    confidence_score = max(0.2, min(1.0, confidence_score))

    if confidence_score > 0.75:
        return "high"
    elif confidence_score > 0.5:
        return "medium"
    else:
        return "low"


def generate_recommendation(prediction: float, features: Dict) -> str:
    """
    Generate rekomendasi berbasis prediksi stok 1 bulan ke depan.

    prediction  = Kebutuhan stok 1 bulan ke depan (output model)
    stok_saat_ini = Stok yang tersedia sekarang
    gap         = Berapa yang harus dipesan agar stok cukup
    """
    stok_saat_ini = features.get('stok_saat_ini', 0)
    safety_stock  = features.get('safety_stock', 1)
    avg_daily     = features.get('avg_daily_sales', 0)

    gap      = prediction - stok_saat_ini
    days_left = round(stok_saat_ini / avg_daily) if avg_daily > 0 else 0

    if gap > safety_stock * 2:
        return f"\u26a0\ufe0f Segera pesan {gap:.0f} unit \u2014 stok tidak cukup untuk 1 bulan"
    elif gap > 0:
        return f"Pesan {gap:.0f} unit (stok saat ini cukup ~{days_left} hari)"
    elif stok_saat_ini >= prediction * 1.2:
        return "\u2705 Stok sangat mencukupi, tidak perlu pesan bulan ini"
    else:
        return "Stok cukup untuk 1 bulan, pantau berkala"


@app.errorhandler(404)
def not_found(error):
    """Handle 404 errors"""
    return jsonify({
        'error': 'Endpoint not found',
        'available_endpoints': [
            'GET  /status',
            'GET  /model/info',
            'POST /predict',
            'POST /predict/batch'
        ]
    }), 404


@app.errorhandler(500)
def server_error(error):
    """Handle 500 errors"""
    return jsonify({
        'error': 'Internal server error',
        'message': str(error)
    }), 500


def initialize_api(model_path: str):
    """Initialize API by loading model"""
    try:
        load_model(model_path)
        logger.info("✅ API initialized successfully")
        return True
    except Exception as e:
        logger.error(f"❌ Failed to initialize API: {str(e)}")
        return False


if __name__ == '__main__':
    import sys
    import os

    # Hugging Face Spaces: ambil MODEL_PATH dari env, fallback ke default
    model_path = os.environ.get('MODEL_PATH', 'model/stock_prediction_model.pkl')

    print("=" * 70)
    print("STOCK PREDICTION FLASK API - DECISION TREE REGRESSOR")
    print("=" * 70)
    print(f"\n🔄 Initializing API with model: {model_path}")

    if initialize_api(model_path):
        print("\n✅ API Ready!")
        print("\n📍 Available Endpoints:")
        print("   GET  http://0.0.0.0:7860/status")
        print("   GET  http://0.0.0.0:7860/model/info")
        print("   POST http://0.0.0.0:7860/predict")
        print("   POST http://0.0.0.0:7860/predict/batch")
        print("\n🚀 Starting Flask server on port 7860 (Hugging Face Spaces)...")
        print("=" * 70)
        # Port 7860 = wajib untuk Hugging Face Spaces
        app.run(debug=False, host='0.0.0.0', port=7860)
    else:
        print("\n❌ Failed to initialize API")
        sys.exit(1)
