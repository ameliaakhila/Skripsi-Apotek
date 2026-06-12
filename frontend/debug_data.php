<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin\Obat;
use App\Models\Admin\StokBatch;
use App\Models\Admin\ObatKeluar;

echo "Total Obat: " . Obat::count() . "\n";
echo "Total StokBatch: " . StokBatch::count() . "\n";
echo "Total ObatKeluar: " . ObatKeluar::count() . "\n";

$obatWithBatch = Obat::whereHas('ObatMasuk')->count(); // Checking via ObatMasuk relation if exists
$obatWithStokBatch = Obat::whereIn('id_obat', StokBatch::pluck('id_obat'))->distinct()->count();

echo "Obat with at least one StokBatch: " . $obatWithStokBatch . "\n";

$sampleObat = Obat::first();
if ($sampleObat) {
    echo "Sample Obat: " . $sampleObat->nama_obat . " (ID: " . $sampleObat->id_obat . ")\n";
    $batches = StokBatch::where('id_obat', $sampleObat->id_obat)->get();
    echo "Batches for sample: " . $batches->count() . "\n";
}
