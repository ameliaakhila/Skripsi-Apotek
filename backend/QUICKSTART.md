# 🚀 Quick Start Guide - Stock Prediction Model

## Prerequisites

- ✅ Python 3.8+
- ✅ pip (package manager)
- ✅ Populated Laravel database with historical data
- ✅ Windows/Mac/Linux (cross-platform)

---

## 🎯 5-Minute Setup

### 1️⃣ Install Dependencies

```bash
cd backend
pip install -r requirements.txt
```

**Expected output:**
```
Successfully installed pandas numpy scikit-learn flask ...
```

---

### 2️⃣ Export & Prepare Data

You can now export data from Laravel and prepare it for training in one step:

```bash
cd backend
python app.py --export --months=12
```

**This will automatically:**
- ✅ Call `php artisan prediksi:export-data` in the frontend
- ✅ Copy the latest export to `backend/dataset/training_data_latest.csv`
- ✅ No manual copying required!

---

### 3️⃣ Run Complete Pipeline

```bash
cd backend

# Run full pipeline (export + prepare + train)
python app.py --full --months=12
```

**This will:**
- ✅ Export fresh data from Laravel
- ✅ Load and clean training data
- ✅ Engineer features
- ✅ Train Decision Tree Regressor
- ✅ Save trained model



**Expected output:**
```
========================================================================
STOCK PREDICTION MODEL TRAINING app - CARD METHOD
========================================================================

[DATA PREPARATION]
✅ Data loaded: 500 records
✅ Data cleaned and validated
✅ Features prepared: 12 features, 500 samples

[MODEL TRAINING]
🌳 Building Initial Decision Tree Regressor (CARD Method)
📚 Training model on training set...
✅ Model trained

[MODEL EVALUATION]
📈 TRAINING SET METRICS:
   MAE: 12.34 units
   RMSE: 18.56 units
   R² Score: 0.8723

🧪 TEST SET METRICS:
   MAE: 15.67 units
   RMSE: 22.45 units
   R² Score: 0.8401

💡 Interpretation:
   ✅ Excellent model performance (R² > 0.7)

✅ MODEL TRAINING COMPLETE
```

---

### 5️⃣ Start Flask API

```bash
# Start the API server (runs on port 5000)
python app.py --api
```

**Expected output:**
```
========================================================================
STOCK PREDICTION FLASK API - DECISION TREE REGRESSOR
========================================================================

✅ API Ready!

📍 Available Endpoints:
   GET  http://localhost:5000/status
   GET  http://localhost:5000/model/info
   POST http://localhost:5000/predict
   POST http://localhost:5000/predict/batch

🚀 Starting Flask server...
 * Running on http://0.0.0.0:5000
```

**Keep this terminal open!**

---

### 6️⃣ Test the API

**In a new terminal:**

```bash
# Test health check
curl http://localhost:5000/status

# Get model info
curl http://localhost:5000/model/info

# Make a prediction
curl -X POST http://localhost:5000/predict \
  -H "Content-Type: application/json" \
  -d '{
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
  }'
```

**Expected response:**
```json
{
  "predicted_stock": 250.5,
  "confidence": "high",
  "recommendation": "Stock quantity sufficient",
  "timestamp": "2026-04-27T10:30:45.123456"
}
```

---

### 7️⃣ Integrate with Laravel

Update `app/Http/Controllers/Manager/PrediksiController.php`:

```php
<?php
namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PrediksiController extends Controller
{
    private $api_url = 'http://localhost:5000';

    public function index()
    {
        // Get all drugs and their predictions
        $drugs = \App\Models\Admin\Obat::all();
        $predictions = [];

        foreach ($drugs as $drug) {
            try {
                // Extract features from database
                $features = $this->extractFeatures($drug->id_obat);

                // Call Flask API
                $response = Http::post("{$this->api_url}/predict", $features);

                if ($response->successful()) {
                    $pred = $response->json();
                    $predictions[] = [
                        'id_obat' => $drug->id_obat,
                        'nama_obat' => $drug->nama_obat,
                        'predicted_stock' => $pred['predicted_stock'],
                        'confidence' => $pred['confidence'],
                        'recommendation' => $pred['recommendation']
                    ];
                }
            } catch (\Exception $e) {
                // Log error, continue with next drug
                \Log::error("Prediction error for drug {$drug->id_obat}: {$e->getMessage()}");
            }
        }

        return view('manager.prediksi-obat', ['predictions' => $predictions]);
    }

    private function extractFeatures($id_obat)
    {
        // TODO: Implement feature extraction from database
        // Reference: app/Console/Commands/ExportPrediksiData.php calculateFeatures()
        return [
            'total_masuk' => 500,
            'total_keluar' => 150,
            'stok_saat_ini' => 350,
            // ... extract all 12 features
        ];
    }
}
```

---

## 📊 Understanding the Output

### Model Metrics

| Metric | Meaning | Target |
|--------|---------|--------|
| **R² Score** | How much variance explained | 0.75-0.90 (Excellent) |
| **MAE** | Average prediction error | < 20 units |
| **RMSE** | Larger errors penalized more | < 30 units |

### Confidence Levels

- 🟢 **HIGH**: Good input data, high transaction frequency
- 🟡 **MEDIUM**: Moderate data quality
- 🔴 **LOW**: Sparse data, high volatility

### Recommendation

- ✅ Stock quantity sufficient
- ⚠️ Urgent restock needed
- 📊 Monitor stock level

---

## 🔄 Retraining the Model (Monthly)

```bash
cd backend

# Run full app (export + prepare + train)
python app.py --full --months=12

# Restart API if running
# (Stop the old one and run: python app.py --api)
```

---

## 🛠️ Troubleshooting

### ❌ "No training data found"

```
Solution: Run export command first
php artisan prediksi:export-data --months=12
```

### ❌ "Model file not found"

```
Solution: Run training first
python app.py --full
```

### ❌ "API connection refused"

```
Solution: Start API in another terminal
python app.py --api
```

### ❌ "Missing features" error

```
Solution: Ensure all 12 features are sent in request
See: backend/README.md for complete feature list
```

---

## 📁 File Structure

```
backend/
├── dataset/
│   ├── training_data.csv          # Exported from Laravel
│   ├── prepared_data.csv          # Cleaned data
│   └── data_preparation.py        # Data prep script
├── model/
│   └── stock_prediction_model.pkl # Trained model
├── api/
│   └── app.py                     # Flask API
├── notebook/
│   ├── model_trainer.py           # Training script
│   └── stock_prediction_model_training.ipynb
├── app.py                    # Main app
├── requirements.txt               # Dependencies
├── README.md                      # Full documentation
└── QUICKSTART.md                  # This file
```

---

## 💡 Next Steps

1. ✅ **Setup complete!** Keep API running
2. 🔗 **Integrate** with Laravel (`PrediksiController`)
3. 📈 **Monitor** model performance
4. 🔄 **Retrain** monthly with fresh data
5. 📚 **Read** `README.md` for advanced features

---

## 🆘 Need Help?

1. Check `backend/README.md` for detailed documentation
2. Review training logs in terminal output
3. Test API endpoints manually with curl
4. Verify feature extraction in `ExportPrediksiData.php`

---

**🎉 You're ready to use the Stock Prediction Model!**

---

**Version:** 1.0  
**Last Updated:** April 27, 2026  
**Status:** ✅ Ready for Production
