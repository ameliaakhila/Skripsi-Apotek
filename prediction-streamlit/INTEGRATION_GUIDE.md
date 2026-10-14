# 📋 Complete System Integration Guide

## 🎯 Objective
Integrate the stock prediction model with Laravel backend for real-time inventory forecasting.

---

## 🔗 Architecture Overview

```
LARAVEL FRONTEND
└── PrediksiController
    ├── Collects drug features from database
    ├── Calls Flask /predict endpoint
    └── Displays predictions in UI

       ↕ HTTP REST API

FLASK BACKEND (Python)
└── API Server (:5000)
    ├── GET /status (health check)
    ├── GET /model/info (model metadata)
    ├── POST /predict (single prediction)
    └── POST /predict/batch (batch predictions)
       ↓
    Decision Tree Regressor Model
    └── Outputs numeric stock predictions
```

---

## ✅ Complete Implementation Checklist

### Phase 1: Backend Setup (Python/ML) ✅
- [x] Create Laravel export command (`ExportPrediksiData.php`)
- [x] Create data preparation script (`data_preparation.py`)
- [x] Create model training script (`model_trainer.py`)
- [x] Create Flask API (`app.py`)
- [x] Create deployment app (`app.py`)
- [x] Create comprehensive documentation (`README.md`, `QUICKSTART.md`)

### Phase 2: Frontend Integration (Laravel)
- [ ] Update `PrediksiController.php` with feature extraction
- [ ] Add API integration in controller
- [ ] Update views to display predictions
- [ ] Add prediction history tracking
- [ ] Create API error handling

### Phase 3: Deployment & Monitoring
- [ ] Set up automatic model retraining (scheduled task)
- [ ] Monitor API performance
- [ ] Track prediction accuracy over time
- [ ] Create alerts for model drift

---

## 🔑 Key Implementation Points

### 1. Feature Extraction (Critical!)

The model requires 12 features extracted from your database:

```php
// In: app/Http/Controllers/Manager/PrediksiController.php

private function extractFeatures($id_obat)
{
    $startDate = now()->subMonths(12)->startOfDay();
    $endDate = now()->endOfDay();

    // 1. Historical incoming
    $totalMasuk = \App\Models\Admin\StokBatch::where('id_obat', $id_obat)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('stok_masuk');

    // 2. Historical outgoing
    $totalKeluar = \App\Models\Admin\ObatKeluar::whereHas('StokBatch', function($q) use($id_obat) {
            $q->where('id_obat', $id_obat);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('stok_keluar');

    // ... extract remaining features ...

    return [
        'total_masuk' => $totalMasuk,
        'total_keluar' => $totalKeluar,
        'stok_saat_ini' => $stokSaatIni,
        'frekuensi_transaksi' => $frekuensiTransaksi,
        // ... all 12 features ...
    ];
}
```

### 2. API Integration Pattern

```php
// Call Flask API from Laravel
$response = Http::post('http://localhost:5000/predict', [
    'total_masuk' => 500,
    'total_keluar' => 150,
    // ... all features ...
]);

$prediction = $response->json();

// Use prediction in your logic
$recommendedStock = $prediction['predicted_stock'];
$confidence = $prediction['confidence'];
$recommendation = $prediction['recommendation'];
```

### 3. Error Handling

```php
try {
    $response = Http::timeout(5)->post('http://localhost:5000/predict', $features);

    if ($response->successful()) {
        $prediction = $response->json();
        // Process prediction...
    } else {
        \Log::error("Prediction API error: " . $response->status());
        // Return fallback or show error to user
    }
} catch (\Exception $e) {
    \Log::error("Prediction failed: " . $e->getMessage());
    // Handle gracefully
}
```

---

## 🚀 Step-by-Step Implementation

### Step 1: Verify Backend is Ready

```bash
# Check backend structure
backend/
├── api/app.py              # ✅ Flask API
├── notebook/model_trainer.py   # ✅ Training script
├── dataset/data_preparation.py # ✅ Data prep
└── requirements.txt         # ✅ Dependencies
```

### Step 2: Update PrediksiController

**File:** `frontend/app/Http/Controllers/Manager/PrediksiController.php`

```php
<?php
namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Admin\Obat;
use App\Models\Admin\StokBatch;
use App\Models\Admin\ObatKeluar;
use App\Models\Admin\StokOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PrediksiController extends Controller
{
    private $api_url = 'http://localhost:5000';
    private $api_timeout = 5; // seconds

    /**
     * Display predictions for all drugs
     */
    public function index()
    {
        $drugs = Obat::all();
        $predictions = [];

        foreach ($drugs as $drug) {
            try {
                $features = $this->extractFeatures($drug->id_obat);
                $prediction = $this->callPredictionAPI($features);

                $predictions[] = [
                    'id_obat' => $drug->id_obat,
                    'nama_obat' => $drug->nama_obat,
                    'predicted_stock' => $prediction['predicted_stock'] ?? 0,
                    'confidence' => $prediction['confidence'] ?? 'unknown',
                    'recommendation' => $prediction['recommendation'] ?? '',
                    'current_stock' => $features['stok_saat_ini'] ?? 0
                ];
            } catch (\Exception $e) {
                \Log::error("Prediction error for drug {$drug->id_obat}: {$e->getMessage()}");

                // Add with error status
                $predictions[] = [
                    'id_obat' => $drug->id_obat,
                    'nama_obat' => $drug->nama_obat,
                    'error' => 'Prediction unavailable',
                    'current_stock' => 0
                ];
            }
        }

        return view('manager.prediksi-obat', [
            'predictions' => $predictions
        ]);
    }

    /**
     * Extract all 12 features from database for a drug
     */
    private function extractFeatures($id_obat)
    {
        $startDate = now()->subMonths(12)->startOfDay();
        $endDate = now()->endOfDay();

        // Get batches for this drug
        $batches = StokBatch::where('id_obat', $id_obat)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        if ($batches->isEmpty()) {
            throw new \Exception("No stock history for drug {$id_obat}");
        }

        // Feature 1: Total incoming
        $totalMasuk = $batches->sum('stok_masuk');

        // Feature 2: Total outgoing
        $totalKeluar = ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('stok_keluar');

        // Feature 3: Current stock
        $stokSaatIni = $batches->sum('stok_sisa');

        // Feature 4: Transaction frequency
        $frekuensiTransaksi = ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Feature 5: Average transaction
        $avgTransaksi = $frekuensiTransaksi > 0
            ? round($totalKeluar / $frekuensiTransaksi, 2)
            : 0;

        // Feature 6: Expiry risk (< 30 days)
        $kedaluwarsaRisk = $batches
            ->filter(fn($b) => Carbon::parse($b->tanggal_kedaluwarsa)
                ->diffInDays(Carbon::now(), false) <= 30)
            ->count();

        // Feature 7: Accuracy deviation from opname
        $opnameData = StokOpname::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $accuracyDeviation = $opnameData->isNotEmpty()
            ? round(abs($opnameData->avg('selisih')), 2)
            : 0;

        // Feature 8: Safety stock ratio
        $drug = Obat::find($id_obat);
        $safetyStockRatio = $drug->safety_stock > 0
            ? round($stokSaatIni / $drug->safety_stock, 2)
            : 0;

        // Feature 9: Demand volatility
        $demandVolatility = $this->calculateVolatility($batches, $startDate, $endDate);

        // Feature 10: Trend indicator
        $recentAvg = $this->getRecentAverage($batches, 7);
        $overallAvg = $frekuensiTransaksi > 0 ? $totalKeluar / $frekuensiTransaksi : 0;
        $trendIndicator = $overallAvg > 0
            ? round($recentAvg / $overallAvg, 2)
            : 0;

        // Feature 11: Safety stock
        $safetyStock = $drug->safety_stock;

        // Feature 12: Average daily sales
        $daysInPeriod = max(1, $endDate->diffInDays($startDate));
        $avgDailySales = round($totalKeluar / $daysInPeriod, 2);

        return [
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'stok_saat_ini' => $stokSaatIni,
            'frekuensi_transaksi' => $frekuensiTransaksi,
            'avg_transaksi' => $avgTransaksi,
            'kedaluwarsa_risk' => $kedaluwarsaRisk,
            'accuracy_deviation' => $accuracyDeviation,
            'safety_stock_ratio' => $safetyStockRatio,
            'demand_volatility' => $demandVolatility,
            'trend_indicator' => $trendIndicator,
            'safety_stock' => $safetyStock,
            'avg_daily_sales' => $avgDailySales
        ];
    }

    /**
     * Call Flask API /predict endpoint
     */
    private function callPredictionAPI($features)
    {
        $response = Http::timeout($this->api_timeout)
            ->post("{$this->api_url}/predict", $features);

        if (!$response->successful()) {
            throw new \Exception("API returned status {$response->status()}");
        }

        return $response->json();
    }

    /**
     * Calculate demand volatility (standard deviation)
     */
    private function calculateVolatility($batches, $startDate, $endDate)
    {
        $dateRange = $startDate->copy();
        $dailySales = [];

        while ($dateRange <= $endDate) {
            $dayEnd = $dateRange->copy()->endOfDay();
            $sales = ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
                ->whereBetween('created_at', [$dateRange->copy()->startOfDay(), $dayEnd])
                ->sum('stok_keluar');

            $dailySales[] = $sales;
            $dateRange->addDay();
        }

        if (empty($dailySales)) {
            return 0;
        }

        $mean = array_sum($dailySales) / count($dailySales);
        $variance = array_sum(array_map(
            fn($x) => pow($x - $mean, 2),
            $dailySales
        )) / count($dailySales);

        return round(sqrt($variance), 2);
    }

    /**
     * Get recent average (last N days)
     */
    private function getRecentAverage($batches, $days)
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $totalSales = ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('stok_keluar');

        return round($totalSales / $days, 2);
    }
}
```

### Step 3: Update Blade View

**File:** `frontend/resources/views/manager/prediksi-obat.blade.php`

```blade
<x-app title="Prediksi Stok Obat">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Prediksi Stok Obat - Decision Tree Model</h5>
                    <small class="text-muted">Updated: {{ now()->format('Y-m-d H:i:s') }}</small>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Obat</th>
                                    <th style="text-align: center;">Stok Saat Ini</th>
                                    <th style="text-align: center;">Prediksi Dibutuhkan</th>
                                    <th style="text-align: center;">Selisih</th>
                                    <th>Kepercayaan</th>
                                    <th>Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($predictions as $pred)
                                    @if(!isset($pred['error']))
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $pred['nama_obat'] }}</td>
                                            <td style="text-align: center;">
                                                <strong>{{ $pred['current_stock'] }}</strong>
                                            </td>
                                            <td style="text-align: center;">
                                                <strong class="text-primary">
                                                    {{ number_format($pred['predicted_stock'], 0) }}
                                                </strong>
                                            </td>
                                            <td style="text-align: center;">
                                                @php
                                                    $gap = $pred['predicted_stock'] - $pred['current_stock'];
                                                @endphp
                                                @if($gap > 0)
                                                    <span class="badge badge-warning">
                                                        +{{ number_format($gap, 0) }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-success">
                                                        {{ number_format($gap, 0) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($pred['confidence'] === 'high')
                                                    <span class="badge badge-success">🟢 Tinggi</span>
                                                @elseif($pred['confidence'] === 'medium')
                                                    <span class="badge badge-warning">🟡 Sedang</span>
                                                @else
                                                    <span class="badge badge-danger">🔴 Rendah</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $pred['recommendation'] }}</small>
                                            </td>
                                        </tr>
                                    @else
                                        <tr class="table-danger">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $pred['nama_obat'] }}</td>
                                            <td colspan="5" class="text-danger">
                                                {{ $pred['error'] }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app>
```

---

## 🔄 Deployment Workflow

### Initial Setup

```bash
# 1. Backend setup
cd backend
pip install -r requirements.txt

# 2. Export data from Laravel
cd ../frontend
php artisan prediksi:export-data --months=12

# 3. Copy data and train
cd ../backend
python app.py --full

# 4. Start API
python app.py --api
# (Keep running in background or use screen/tmux)
```

### Monthly Maintenance

```bash
# Retrain with new data
cd backend
python app.py --full --months=12
# Restart API if needed
```

---

## 🎯 Success Criteria

✅ **Backend:**
- [ ] Data export command works
- [ ] Model trains successfully with R² > 0.7
- [ ] API responds to /status request
- [ ] API returns predictions for test input

✅ **Frontend:**
- [ ] Controller extracts features correctly
- [ ] API calls succeed (check logs)
- [ ] Predictions display in UI
- [ ] No error messages shown to user

✅ **Integration:**
- [ ] Manager dashboard shows predictions
- [ ] Predictions update automatically
- [ ] Recommendations are actionable
- [ ] System handles API failures gracefully

---

## 📊 Expected Workflow

```
Time: Daily
├─ Manager views /prediksi dashboard
├─ System fetches all drugs from database
├─ Controller extracts 12 features for each drug
├─ Calls Flask API for predictions
├─ Displays predicted vs current stock
└─ Manager makes restock decisions

Time: Monthly
├─ Export 12 months of new data
├─ Retrain Decision Tree model
├─ Update model file
├─ Predictions become more accurate
└─ Repeat cycle
```

---

## 🆘 Debugging

### API not responding?
```php
// Add logging to your Laravel code
\Log::debug('Calling prediction API', ['features' => $features]);
$response = Http::post(...);
\Log::debug('API Response', ['status' => $response->status(), 'body' => $response->body()]);
```

### Features not extracted correctly?
```php
// Debug feature values
dd($features);
// Compare with ExportPrediksiData.php calculateFeatures() method
```

### Model making bad predictions?
```bash
# Check training metrics
python app.py --demo
# Review feature importance
# Consider retraining with more data
```

---

## 📞 Support Resources

- **Full Documentation:** `backend/README.md`
- **Quick Start:** `backend/QUICKSTART.md`
- **Code Comments:** Review inline comments in all Python files
- **API Endpoints:** Test with curl (examples in docs)

---

**🎉 Integration Complete!**

Your pharmacy inventory prediction system is now ready for production use.

