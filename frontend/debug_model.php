<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\ObatMasuk;

$item = ObatMasuk::first();

if ($item) {
    echo "Class: " . get_class($item) . "\n";
    echo "Attributes:\n";
    print_r($item->getAttributes());
} else {
    echo "No records found in tb_obat_masuk.\n";
}
