<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ML\StockPredictionService;

try {
    $service = app(StockPredictionService::class);
    $data = $service->getAllPredictions();
    $statuses = $data->pluck('status')->unique()->toArray();
    echo "Unique statuses found in data: " . json_encode($statuses) . "\n";
    
    $first10 = $data->take(10)->map(function($item) {
        return [
            'nama_obat' => $item['nama_obat'],
            'status' => $item['status'] ?? 'NULL',
            'error' => $item['error'] ?? 'NONE'
        ];
    });
    echo "First 10 items status check:\n" . json_encode($first10, JSON_PRETTY_PRINT) . "\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
