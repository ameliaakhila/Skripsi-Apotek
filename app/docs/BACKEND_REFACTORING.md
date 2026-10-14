# Backend Refactoring - Stock Prediction System

## Overview

This document explains the refactored backend architecture for the stock prediction system, emphasizing clean architecture, modular design, and proper separation of concerns.

## Architecture Pattern

The system follows a **Service Layer Pattern** with dependency injection:

```
Controller → Service Layer → Database Models → ML API
             ↓
        Prediction Service (Orchestration)
             ↓
        Feature Service (Extraction)
        ML Service (API Communication)
        Export Service (Data Management)
```

## Core Components

### 1. StockFeatureService (`app/Services/ML/StockFeatureService.php`)

**Responsibility**: Extract 12 ML features from database for a given drug

**Key Methods**:
- `extractFeatures($id_obat, $months=12)`: Extract all 12 features for single drug
- `extractAllFeatures($months=12)`: Extract features for all drugs
- `getLastMonthSalesSummary($id_obat)`: Get sales data for last month
- `calculateMonthlyVolatility()`: Calculate demand standard deviation
- `getRecentAverage()`: Calculate average for recent days

**Features Extracted** (12 total):
1. **total_masuk** (int): Historical incoming stock
2. **total_keluar** (int): Historical outgoing stock (sales)
3. **stok_saat_ini** (int): Current stock level
4. **frekuensi_transaksi** (int): Transaction frequency (last month focus)
5. **avg_transaksi** (float): Average per transaction
6. **kedaluwarsa_risk** (int): Batches expiring < 30 days
7. **accuracy_deviation** (float): Inventory accuracy from opname
8. **safety_stock_ratio** (float): Current stock / safety threshold
9. **demand_volatility** (float): Standard deviation of daily sales
10. **trend_indicator** (float): Recent vs overall average ratio
11. **safety_stock** (int): Minimum safe stock threshold
12. **avg_daily_sales** (float): Average daily sales (last month focus)

**Last Month Focus**:
```php
$lastMonthStart = now()->subMonth()->startOfDay();
$lastMonthEnd = now()->endOfDay();
// All queries filter on tanggal_keluar between these dates
```

### 2. MLPredictionService (`app/Services/ML/MLPredictionService.php`)

**Responsibility**: Communicate with Flask REST API for predictions

**Key Methods**:
- `isAPIAvailable()`: Check if Flask API is accessible
- `predict(array $features)`: Get prediction for single drug
- `predictBatch(array $predictions_list)`: Get batch predictions
- `getModelInfo()`: Retrieve model metadata

**API Communication**:
- Base URL: `http://localhost:5000`
- Timeout: 5 seconds
- Error handling: Graceful fallback on API unavailability

### 3. StockPredictionService (`app/Services/ML/StockPredictionService.php`)

**Responsibility**: Orchestrate feature extraction and predictions with caching and fallback logic

**Key Methods**:
- `getAllPredictions(bool $use_cache=true)`: Get predictions for all drugs
- `getDrugPrediction($id_obat)`: Get prediction for single drug
- `calculateDatabasePrediction()`: Fallback calculation without ML
- `getStockStatus()`: Determine status (aman, menipis, habis)
- `getPredictionStats()`: Get summary statistics
- `clearCache()`: Force regenerate predictions

**Hybrid Mode Logic**:
```php
// Try ML prediction
if (MLService::isAPIAvailable()) {
    $prediction = MLService::predict($features);
} else {
    // Fallback to database logic
    $prediction = calculateDatabasePrediction($features);
}
```

**Fallback Prediction Formula**:
```
predicted_stock = (avg_daily_sales × 30) + safety_stock + demand_volatility
```

### 4. DataExportService (`app/Services/ML/DataExportService.php`)

**Responsibility**: Export features to CSV for ML model training

**Key Methods**:
- `exportToCSV($months=12, $filename=null)`: Export all features to CSV
- `getExportedFiles()`: List available export files
- `deleteExportedFile()`: Remove exported file
- `getLatestExportedFile()`: Get most recent export

**Export Location**: `storage/training_data_prediksi_stok/`

**CSV Format**:
```
id_obat,nama_obat,total_masuk,total_keluar,stok_saat_ini,...
1,"Paracetamol 500mg",100,45,55,...
2,"Ibuprofen 200mg",150,80,70,...
```

## Updated Controller

### PrediksiController (`app/Http/Controllers/Manager/PrediksiController.php`)

**Simplified Responsibilities** (delegated to services):
- `index()`: Display prediction dashboard
- `generatePrediction()`: Trigger prediction regeneration
- `getDrugPrediction()`: AJAX endpoint for single drug
- `getLastMonthSummary()`: AJAX endpoint for monthly summary

**Dependency Injection**:
```php
public function __construct(StockPredictionService $predictionService)
{
    $this->predictionService = $predictionService;
}
```

## Service Provider

### MLServiceProvider (`app/Providers/MLServiceProvider.php`)

**Purpose**: Register all ML services in container as singletons

**Services Registered**:
1. `StockFeatureService` - Feature extraction
2. `MLPredictionService` - API communication
3. `StockPredictionService` - Orchestration
4. `DataExportService` - Export management

**Bootstrap Integration** (`bootstrap/app.php`):
```php
->withProviders([
    \App\Providers\MLServiceProvider::class,
])
```

## Frontend Integration

### Blade View Updates (`resources/views/manager/prediksi-obat.blade.php`)

**New Features**:
- **"Hitung Prediksi" Button**: Manually trigger prediction regeneration
- **API Status Badge**: Shows ML Model Aktif or Mode Database
- **Statistics Cards**: Total drugs, safe, low-stock, out-of-stock counts
- **Success/Error Messages**: Flash alerts for user feedback
- **Source Indicator**: Shows "Prediksi ML" or "Dari DB" for each prediction

### Routes Updated (`routes/web.php`)

**New Routes**:
```php
Route::post('/prediksi/generate', 'PrediksiController@generatePrediction');
Route::get('/prediksi/drug/{id_obat}', 'PrediksiController@getDrugPrediction');
Route::get('/prediksi/summary/{id_obat}', 'PrediksiController@getLastMonthSummary');
```

## Data Flow

### Single Drug Prediction
```
1. User requests prediction
   ↓
2. PrediksiController::getDrugPrediction()
   ↓
3. StockPredictionService::getDrugPrediction()
   ↓
4. StockFeatureService::extractFeatures()
   └─ Query database for last 12 months
   └─ Calculate 12 features
   └─ Focus on last month sales data
   ↓
5. MLPredictionService::predict() or calculateDatabasePrediction()
   └─ Try Flask API
   └─ Fallback to database formula
   ↓
6. Format and return prediction
```

### Batch Prediction (Dashboard)
```
1. User accesses /prediksi dashboard
   ↓
2. PrediksiController::index()
   ↓
3. StockPredictionService::getAllPredictions(use_cache: true)
   ├─ Check cache
   └─ If missing:
      └─ Iterate all drugs
      └─ Extract features for each
      └─ Get predictions (ML or fallback)
      └─ Cache results (1 hour)
   ↓
4. Format data for view
   ↓
5. Display dashboard with table + statistics
```

### Manual Regeneration
```
1. User clicks "Hitung Prediksi"
   ↓
2. POST /prediksi/generate
   ↓
3. PrediksiController::generatePrediction()
   ↓
4. StockPredictionService::clearCache()
   ↓
5. Redirect back (cache will regenerate on next view)
```

## Error Handling

### Graceful Degradation
- **API Unavailable**: Automatically use database prediction logic
- **No Features Extracted**: Return null, skip drug in results
- **Database Error**: Log exception, continue with other drugs
- **Invalid Prediction**: Use fallback calculation

### Logging
All errors logged to `storage/logs/laravel.log`:
```php
Log::error("Feature extraction error for drug {$id_obat}: {$e->getMessage()}");
Log::warning("ML API unavailable: {$e->getMessage()}");
```

## Performance Optimization

### Caching
- **Predictions Cache**: 1 hour TTL
- **Cache Key**: `stock_predictions_all`
- **Manual Clear**: Via `clearCache()` method or "Hitung Prediksi" button

### Query Optimization
- **Eager Loading**: Uses relationships to minimize queries
- **Date Filtering**: Leverages database indexes on tanggal_keluar
- **Collection Methods**: Map/filter operations on small datasets

## Database Integration

### Models Used
- `Obat`: Drug master data, relations
- `StokBatch`: Stock batches with incoming/outgoing
- `ObatKeluar`: Sales transactions with dates
- `StokOpname`: Inventory count for accuracy
- `MutasiStok`: Stock mutations (future use)

### Key Queries
```php
// Get batches for last 12 months
StokBatch::where('id_obat', $id_obat)
    ->whereBetween('created_at', [$startDate, $endDate])
    ->get();

// Get sales for last month
ObatKeluar::whereIn('id_stok_batch', $batchIds)
    ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
    ->sum('stok_keluar');
```

## Configuration

### Environment Variables (Optional)
```env
ML_API_URL=http://localhost:5000
ML_API_TIMEOUT=5
ML_CACHE_TTL=3600
```

### Current Hardcoded Values
- API URL: `http://localhost:5000`
- Timeout: 5 seconds
- Cache TTL: 1 hour
- Historical period: 12 months
- Last month focus: 30 days

## Testing the System

### 1. Feature Extraction
```bash
php artisan tinker
>>> $service = app(\App\Services\ML\StockFeatureService::class);
>>> $features = $service->extractFeatures(1);
>>> dd($features);
```

### 2. API Availability
```php
$mlService = app(\App\Services\ML\MLPredictionService::class);
echo $mlService->isAPIAvailable() ? "Online" : "Offline";
```

### 3. All Predictions
```php
$predService = app(\App\Services\ML\StockPredictionService::class);
$predictions = $predService->getAllPredictions();
echo $predictions->count() . " predictions loaded";
```

### 4. Manual Regeneration
```bash
curl -X POST http://localhost:8000/manager/prediksi/generate
```

## Best Practices Implemented

1. **Separation of Concerns**: Each service has single responsibility
2. **Dependency Injection**: Services injected via constructor
3. **Service Container**: Uses Laravel's singleton pattern
4. **Error Handling**: Try-catch with logging throughout
5. **Caching**: Reduces database queries for repeat requests
6. **Last Month Focus**: Prioritizes recent sales data for better predictions
7. **Fallback Logic**: System functions even if API is unavailable
8. **Modular Design**: Easy to add new prediction methods
9. **Type Hints**: PHP 7+ strict typing throughout
10. **Documentation**: Inline comments explaining logic

## Future Enhancements

1. **Advanced Analytics Service**: Add trend analysis, seasonality
2. **Alert System**: Notify on critical stock levels
3. **Multi-branch Support**: Handle multiple pharmacy branches
4. **Custom Thresholds**: User-configurable safety stock levels
5. **Prediction History**: Track predictions over time
6. **Export Scheduling**: Automated daily/weekly exports
7. **API Webhooks**: Real-time predictions on stock changes
8. **Mobile API**: JSON endpoints for mobile app

## Migration Notes

If migrating from old system:

1. **Models**: Already updated to use new services
2. **Routes**: Added new prediction endpoints
3. **Views**: Updated with new button and statistics
4. **Providers**: MLServiceProvider automatically boots
5. **Cache**: Clear manually if needed via command or UI button
