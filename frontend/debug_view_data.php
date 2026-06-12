<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Manager\PrediksiController;

try {
    echo "Running controller...\n";
    $controller = app(PrediksiController::class);
    $response = $controller->index();
    $data = $response->getData();
    
    echo "Data count: " . count($data['data']) . "\n";
    if (count($data['data']) > 0) {
        $first = $data['data']->first();
        echo "First item type: " . gettype($first) . "\n";
        echo "First item data: " . json_encode($first) . "\n";
        
        $stats = $data['stats'];
        echo "Stats: " . json_encode($stats) . "\n";
    } else {
        echo "Data is EMPTY\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
