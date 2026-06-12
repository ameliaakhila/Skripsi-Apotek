# Quick Start Guide - Refactored Backend

## For Developers

### Quick Access Reference

**Services Location**: `app/Services/ML/`
- `StockFeatureService.php` - Extract features from database
- `MLPredictionService.php` - Call Flask API
- `StockPredictionService.php` - Get predictions (orchestrator)
- `DataExportService.php` - Export to CSV

**Controller**: `app/Http/Controllers/Manager/PrediksiController.php`

**Routes**: `routes/web.php` (lines ~180)

**View**: `resources/views/manager/prediksi-obat.blade.php`

**Provider**: `app/Providers/MLServiceProvider.php`

---

## Using Services in Your Code

### Get Predictions (Dashboard)
```php
$predService = app(\App\Services\ML\StockPredictionService::class);

// Get all predictions with cache
$predictions = $predService->getAllPredictions(use_cache: true);

// Get stats
$stats = $predService->getPredictionStats();
// Returns: [total_drugs, status_aman, status_menipis, status_habis, api_available]

// Clear cache (force regenerate)
$predService->clearCache();
```

### Get Single Drug Prediction
```php
$predService = app(\App\Services\ML\StockPredictionService::class);
$prediction = $predService->getDrugPrediction($id_obat);

// Returns: [
//     id_obat, nama_obat, total_masuk, total_keluar, stok_saat_ini,
//     frekuensi_transaksi, avg_transaksi, kedaluwarsa_risk,
//     accuracy_deviation, safety_stock_ratio, demand_volatility,
//     trend_indicator, safety_stock, avg_daily_sales, last_month_total_sales,
//     predicted_stock, confidence, recommendation, source
// ]
```

### Extract Features Only
```php
$featureService = app(\App\Services\ML\StockFeatureService::class);

// Single drug (12 features)
$features = $featureService->extractFeatures($id_obat, $months = 12);

// All drugs
$allFeatures = $featureService->extractAllFeatures($months = 12);

// Last month summary
$summary = $featureService->getLastMonthSalesSummary($id_obat);
// Returns: [total_sales, avg_daily_sales, max_daily_sales, transaction_count, days_active]
```

### Check ML API Status
```php
$mlService = app(\App\Services\ML\MLPredictionService::class);

if ($mlService->isAPIAvailable()) {
    echo "ML API is online!";
} else {
    echo "Using database predictions (fallback)";
}
```

### Export Data to CSV
```php
$exportService = app(\App\Services\ML\DataExportService::class);

// Export to CSV
$path = $exportService->exportToCSV($months = 12);
echo "Exported to: " . $path;

// Get exported files list
$files = $exportService->getExportedFiles();
// Returns collection with: name, path, size, date

// Get latest export
$latest = $exportService->getLatestExportedFile();

// Delete export
$exportService->deleteExportedFile('training_data_2024-01-01_120000.csv');
```

---

## In Blade View

### Trigger Prediction Regeneration
```blade
<form action="{{ route('prediksi.generate') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-redo"></i> Hitung Prediksi
    </button>
</form>
```

### Display Predictions
```blade
@foreach ($data as $item)
    <tr>
        <td>{{ $item['nama_obat'] }}</td>
        <td>{{ $item['stok_sisa'] }}</td>
        <td>{{ $item['stok_prediksi'] }}</td>
        <td>{{ $item['confidence'] }}</td>
        <td>{{ $item['source'] }}</td> <!-- "ml" or "database" -->
    </tr>
@endforeach
```

### Display Statistics
```blade
<h3>Total: {{ $stats['total_drugs'] }}</h3>
<h3>Aman: {{ $stats['status_aman'] }}</h3>
<h3>Menipis: {{ $stats['status_menipis'] }}</h3>
<h3>Habis: {{ $stats['status_habis'] }}</h3>
<p>API: {{ $stats['api_available'] ? 'Active' : 'Offline' }}</p>
```

---

## AJAX Endpoints

### Get Single Drug Prediction
```javascript
fetch('/manager/prediksi/drug/1')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log(data.data); // Prediction data
        }
    });
```

### Get Last Month Summary
```javascript
fetch('/manager/prediksi/summary/1')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log(data.data); // {total_sales, avg_daily_sales, ...}
        }
    });
```

---

## Database Queries Behind The Scenes

### Feature Extraction Queries
```php
// 1. Get batches for drug (last 12 months)
SELECT * FROM tb_stok_batch
WHERE id_obat = ? AND created_at BETWEEN ? AND ?

// 2. Get sales transactions
SELECT SUM(stok_keluar) FROM tb_obat_keluar
WHERE id_stok_batch IN (...) AND created_at BETWEEN ? AND ?

// 3. Get inventory accuracy
SELECT AVG(selisih) FROM tb_stok_opname
WHERE id_stok_batch IN (...) AND created_at BETWEEN ? AND ?
```

### Last Month Focus
```php
$lastMonthStart = now()->subMonth()->startOfDay();
$lastMonthEnd = now()->endOfDay();

// All queries filter ObatKeluar.tanggal_keluar to last month
```

---

## Debugging

### Test Feature Extraction
```bash
php artisan tinker

# Single drug
$s = app(\App\Services\ML\StockFeatureService::class);
$f = $s->extractFeatures(1);
dd($f);

# Check for null (no data)
$f = $s->extractFeatures(999); // Non-existent drug
// Returns: null
```

### Check API
```bash
# Check API status
curl http://localhost:5000/status

# Expected response:
# {"status": "ok", "timestamp": "2024-01-01T12:00:00", "model_loaded": true}
```

### Monitor Cache
```bash
# Clear cache
php artisan cache:clear

# Check cache contents
php artisan tinker
>>> Cache::get('stock_predictions_all')
```

### View Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log

# Search for errors
grep "Feature extraction error" storage/logs/laravel.log
grep "ML API unavailable" storage/logs/laravel.log
```

---

## Common Tasks

### Task 1: Add New Prediction Logic
```php
// In StockPredictionService
private function myCustomPrediction(array $features) {
    $custom_calc = $features['avg_daily_sales'] * 25;
    return round($custom_calc, 0);
}
```

### Task 2: Change Cache Duration
```php
// In StockPredictionService::getAllPredictions()
return Cache::remember('stock_predictions_all', 
    now()->addHours(2),  // Change from 1 to 2 hours
    function () { ... }
);
```

### Task 3: Extend Features Extracted
```php
// In StockFeatureService::extractFeatures()
$newFeature = /* calculate something */;
return array_merge($features, [
    'new_feature_name' => $newFeature,
]);
```

### Task 4: Add New Export Format
```php
// In DataExportService
public function exportToJSON($months = 12) {
    $features = $this->featureService->extractAllFeatures($months);
    return json_encode($features);
}
```

---

## Performance Tips

1. **Use Caching**: Always call `getAllPredictions(use_cache: true)`
2. **Batch Operations**: Extract all features together, not individually
3. **Index Queries**: Ensure `tb_obat_keluar.tanggal_keluar` is indexed
4. **Monitor Cache**: Check if cache is actually being used
5. **API Timeout**: Set reasonable timeout (5 seconds good default)

---

## When Something Goes Wrong

| Problem | Solution |
|---------|----------|
| API unavailable | Check Flask server: `curl http://localhost:5000/status` |
| No predictions shown | Check: features extracted? API called? Database has data? |
| Slow dashboard | Clear cache: `php artisan cache:clear` |
| Wrong predictions | Check last-month sales data in database |
| Features extraction fails | Verify database tables exist and have data |
| Memory issues | Reduce `$months` parameter or reduce batch size |

---

## File Organization

```
app/
  ├── Services/ML/
  │   ├── StockFeatureService.php     ← Feature extraction
  │   ├── MLPredictionService.php     ← API communication
  │   ├── StockPredictionService.php  ← Orchestration
  │   └── DataExportService.php       ← CSV export
  ├── Providers/
  │   └── MLServiceProvider.php       ← Dependency injection
  └── Http/Controllers/Manager/
      └── PrediksiController.php      ← Request handling
routes/
  └── web.php                         ← Routes defined
resources/views/manager/
  └── prediksi-obat.blade.php         ← UI display
docs/
  ├── BACKEND_REFACTORING.md          ← Architecture docs
  └── REFACTORING_SUMMARY.md          ← Implementation summary
```

---

## Key Concepts

**Dependency Injection**: Services injected via constructor
```php
public function __construct(StockPredictionService $service) {
    $this->service = $service;
}
```

**Service Container**: All services registered as singletons
```php
$service = app(\App\Services\ML\StockPredictionService::class);
```

**Caching**: Results cached for 1 hour, manual clear option
```php
Cache::remember('key', ttl, callback);
```

**Hybrid Mode**: Works with or without ML API
```php
if (API available) use API else use database formula
```

**Last Month Focus**: Recent data prioritized in features
```php
$lastMonthStart = now()->subMonth()->startOfDay();
```

---

## Support

For detailed documentation see:
- `docs/BACKEND_REFACTORING.md` - Full architecture
- `docs/REFACTORING_SUMMARY.md` - Implementation details
- This file - Quick reference

Questions? Check code comments in service files - they're well-documented!
