<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\ObatMasuk;

$data = ObatMasuk::with('Obat')->simplePaginate(15);
$item = $data->items()[0];

echo "Item type: " . gettype($item) . "\n";
if (is_array($item)) {
    echo "Is Array. Has kode_batch? " . (isset($item['kode_batch']) ? 'Yes' : 'No') . "\n";
} else {
    echo "Is Object. Has kode_batch? " . (isset($item->kode_batch) ? 'Yes' : 'No') . "\n";
    echo "Value: " . $item->kode_batch . "\n";
}
