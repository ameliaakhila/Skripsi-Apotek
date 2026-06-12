<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\ObatKeluar;

$item = ObatKeluar::with(['StokBatch', 'ObatMasuk.Obat'])->first();

if ($item) {
    echo "Item type: " . gettype($item) . "\n";
    echo "StokBatch type: " . gettype($item->StokBatch) . "\n";
    
    // In Blade, you might be doing something like $item['StokBatch']->kode_batch
    // If $item is an array, you need to access it as $item['StokBatch']
    // If $item is an object, you need $item->StokBatch
    
    if (is_array($item->StokBatch)) {
        echo "StokBatch is an ARRAY. Access it as \$item['StokBatch']['kode_batch']\n";
    } elseif (is_object($item->StokBatch)) {
        echo "StokBatch is an OBJECT. Access it as \$item->StokBatch->kode_batch\n";
    }
} else {
    echo "No items found.";
}
