<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Admin\ObatKeluar;
use App\Models\Admin\StokBatch;

echo "=== CEK DATA TRANSAKSI ===" . PHP_EOL;
echo "ObatKeluar total : " . ObatKeluar::count() . PHP_EOL;
echo "StokBatch total  : " . StokBatch::count() . PHP_EOL;

$lastMonth = now()->subMonth()->startOfDay();
echo "ObatKeluar bulan lalu: " . ObatKeluar::where('created_at', '>=', $lastMonth)->count() . PHP_EOL;
echo "ObatKeluar 12 bulan  : " . ObatKeluar::where('created_at', '>=', now()->subMonths(12)->startOfDay())->count() . PHP_EOL;

echo PHP_EOL . "=== 5 TRANSAKSI TERBARU ===" . PHP_EOL;
$samples = ObatKeluar::latest()->take(5)->get(['id_stok_batch', 'stok_keluar', 'created_at']);
foreach ($samples as $s) {
    echo "  batch_id:{$s->id_stok_batch} | qty:{$s->stok_keluar} | at:{$s->created_at}" . PHP_EOL;
}

echo PHP_EOL . "=== RENTANG TANGGAL TRANSAKSI ===" . PHP_EOL;
$oldest = ObatKeluar::oldest()->first();
$newest = ObatKeluar::latest()->first();
if ($oldest && $newest) {
    echo "Oldest: {$oldest->created_at}" . PHP_EOL;
    echo "Newest: {$newest->created_at}" . PHP_EOL;
} else {
    echo "Tidak ada data ObatKeluar!" . PHP_EOL;
}

echo PHP_EOL . "=== CEK FITUR PER OBAT (id_obat=1) ===" . PHP_EOL;
$batches = StokBatch::where('id_obat', 1)->get();
echo "Batches untuk obat 1: " . $batches->count() . PHP_EOL;
if ($batches->isNotEmpty()) {
    $batchIds = $batches->pluck('id_stok_batch');
    $sales = ObatKeluar::whereIn('id_stok_batch', $batchIds)->get();
    echo "Total sales records: " . $sales->count() . PHP_EOL;
    echo "Total stok_keluar  : " . $sales->sum('stok_keluar') . PHP_EOL;
    echo "Last month sales   : " . $sales->where('created_at', '>=', $lastMonth)->count() . " transaksi" . PHP_EOL;
}
