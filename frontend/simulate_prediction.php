<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ML\StockPredictionService;

try {
    echo "Starting simulation...\n";
    $service = app(StockPredictionService::class);
    $data = $service->getAllPredictions(false); // Force compute
    echo "Calculated raw predictions: " . $data->count() . "\n";
    
    echo "Calculating stats...\n";
    $stats = $service->getPredictionStats();
    echo "Stats total drugs: " . $stats['total_drugs'] . "\n";
    
    echo "Formatting data...\n";
    // We need PrediksiController to access formatPredictionData, or just simulate it
    $formattedData = $data->map(function ($prediction) use ($service) {
        $current_stock = $prediction['stok_saat_ini'] ?? 0;
        $predicted_stock = $prediction['predicted_stock'] ?? $current_stock;
        $safety_stock = $prediction['safety_stock'] ?? 10;

        return [
            'id_obat' => $prediction['id_obat'] ?? null,
            'nama_obat' => $prediction['nama_obat'] ?? 'Unknown',
            'stok_awal' => $prediction['total_masuk'] ?? 0,
            'stok_terjual' => $prediction['total_keluar'] ?? 0,
            'stok_sisa' => (int) $current_stock,
            'stok_prediksi' => (int) $predicted_stock,
            'safety_stock' => (int) $safety_stock,
            'confidence' => $prediction['confidence'] ?? 'medium',
            'recommendation' => $prediction['recommendation'] ?? '-',
            'status' => $service->getStockStatus($current_stock, $safety_stock),
            'last_month_sales' => $prediction['last_month_total_sales'] ?? 0,
            'avg_daily_sales' => $prediction['avg_daily_sales'] ?? 0,
            'source' => $prediction['source'] ?? 'ml',
            'is_prediction' => true,
        ];
    });
    echo "Success! Formatted items: " . $formattedData->count() . "\n";
    
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
