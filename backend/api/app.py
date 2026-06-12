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
import json
from pathlib import Path
from datetime import datetime
from typing import Dict, List, Union

from flask import Flask, request, jsonify
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


def prepare_input_vector(data: Dict, feature_names: List[str]) -> np.ndarray:
    """Prepare input vector in correct feature order"""
    return np.array([float(data[f]) for f in feature_names]).reshape(1, -1)


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
        'features_count': len(_feature_names),
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

        return jsonify({
            'predicted_stock': float(prediction),
            'confidence': confidence,
            'recommendation': recommendation,
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

        # 2. Perform matrix prediction (The fast part)
        if X_list:
            X_matrix = np.array(X_list)
            batch_predictions = _model.predict(X_matrix)
            
            # 3. Process results
            for i, idx in enumerate(valid_indices):
                prediction = float(batch_predictions[i])
                item = predictions_list[idx]
                results[idx] = {
                    'id_obat': item.get('id_obat'),
                    'predicted_stock': prediction,
                    'confidence': calculate_confidence(prediction, item)
                }

        return jsonify({
            'results': results,
            'count': len(results),
            'timestamp': datetime.now().isoformat()
        }), 200

    except Exception as e:
        logger.error(f"Batch prediction error: {str(e)}")
        return jsonify({'error': str(e)}), 500


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
    """Generate actionable recommendation based on prediction"""
    stok_saat_ini = features.get('stok_saat_ini', 0)
    safety_stock = features.get('safety_stock', 1)

    predicted_required = prediction
    stock_gap = predicted_required - stok_saat_ini

    if stock_gap > safety_stock:
        return f"⚠️ Urgent restock needed. Gap: {stock_gap:.0f} units"
    elif stock_gap > 0:
        return f"Restock recommended. Required: {predicted_required:.0f} units"
    elif stok_saat_ini >= predicted_required * 1.2:
        return "✅ Stock level sufficient. No immediate action needed"
    else:
        return "📊 Monitor stock level"


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

    # Get model path from argument or use default
    model_path = sys.argv[1] if len(sys.argv) > 1 else 'model/stock_prediction_model.pkl'

    print("=" * 70)
    print("STOCK PREDICTION FLASK API - DECISION TREE REGRESSOR")
    print("=" * 70)
    print(f"\n🔄 Initializing API with model: {model_path}")

    if initialize_api(model_path):
        print("\n✅ API Ready!")
        print("\n📍 Available Endpoints:")
        print("   GET  http://localhost:5000/status")
        print("   GET  http://localhost:5000/model/info")
        print("   POST http://localhost:5000/predict")
        print("   POST http://localhost:5000/predict/batch")
        print("\n🚀 Starting Flask server...")
        print("=" * 70)
        app.run(debug=False, host='0.0.0.0', port=5000)
    else:
        print("\n❌ Failed to initialize API")
        sys.exit(1)
