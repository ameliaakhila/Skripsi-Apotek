<?php
/**
 * Export Training Data dari Database → CSV untuk training model ML
 *
 * Script ini mengekspor data transaksi obat dalam format yang kompatibel
 * dengan model_trainer.py dan data_preparation.py.
 *
 * PENGGUNAAN:
 *   php artisan tinker < export_training_data.php
 *   ATAU jalankan via route artisan custom
 *
 * OUTPUT:
 *   backend/dataset/training_data_from_db.csv
 *
 * FORMAT KOLOM OUTPUT (8 fitur + target):
 *   total_masuk, total_keluar, stok_saat_ini, frekuensi_transaksi,
 *   demand_volatility, avg_transaksi, avg_daily_sales, safety_stock,
 *   predicted_required_stock (TARGET = avg_daily_sales * 30 + safety_stock)
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Admin\Obat;
use App\Models\Admin\ObatKeluar;
use App\Models\Admin\StokBatch;
use App\Models\Admin\StokOpname;
use Carbon\Carbon;

// ── Konfigurasi periode ────────────────────────────────────────────────────
$MONTHS_HISTORY  = 12;  // Periode historis untuk total_masuk/total_keluar
$MONTHS_SALES    = 3;   // Periode untuk fitur transaksi (3 bulan terakhir)
$OUTPUT_PATH     = __DIR__ . '/../backend/dataset/training_data_from_db.csv';

// ── Setup periode tanggal ─────────────────────────────────────────────────
$endDate      = now()->endOfDay();
$startDate    = $endDate->copy()->subMonths($MONTHS_HISTORY)->startOfDay();
$periodStart  = now()->subMonths($MONTHS_SALES)->startOfDay();
$periodEnd    = now()->endOfDay();
$periodDays   = max(1, $periodStart->diffInDays($periodEnd));

echo "==========================================================\n";
echo " Export Training Data — Apotek Stock Prediction\n";
echo "==========================================================\n";
echo "Periode historis : {$startDate->format('d M Y')} – {$endDate->format('d M Y')}\n";
echo "Periode transaksi: {$periodStart->format('d M Y')} – {$periodEnd->format('d M Y')} ({$MONTHS_SALES} bulan)\n\n";

// ── Bulk fetch data ───────────────────────────────────────────────────────
$drugs = Obat::all();
echo "Total obat : {$drugs->count()}\n";

// Bulk fetch semua batches
$allBatches = StokBatch::select('id_stok_batch', 'id_obat', 'stok_masuk', 'stok_sisa', 'tanggal_kedaluwarsa')->get();
$batchToDrugMap = $allBatches->pluck('id_obat', 'id_stok_batch')->toArray();
$batchIds       = array_keys($batchToDrugMap);
$groupedBatches = $allBatches->groupBy('id_obat');

// Bulk fetch sales — 3 bulan terakhir
$allSales = ObatKeluar::whereIn('id_stok_batch', $batchIds)
    ->where('created_at', '>=', $periodStart)
    ->select('id_stok_batch', 'stok_keluar', 'created_at')
    ->get();

$salesByDrug = $allSales->groupBy(function ($sale) use ($batchToDrugMap) {
    return $batchToDrugMap[$sale->id_stok_batch] ?? 0;
});

// Bulk fetch sales — historis 12 bulan
$historicalSales = ObatKeluar::whereIn('id_stok_batch', $batchIds)
    ->whereBetween('created_at', [$startDate, $endDate])
    ->select('id_stok_batch', 'stok_keluar', 'created_at')
    ->get();

$historicalByDrug = $historicalSales->groupBy(function ($sale) use ($batchToDrugMap) {
    return $batchToDrugMap[$sale->id_stok_batch] ?? 0;
});

echo "Total transaksi 3 bulan: {$allSales->count()}\n";
echo "Total transaksi 12 bulan: {$historicalSales->count()}\n\n";

// ── Proses setiap obat ────────────────────────────────────────────────────
$rows    = [];
$skipped = 0;

foreach ($drugs as $drug) {
    $drugId      = (int) $drug->id_obat;
    $drugBatches = $groupedBatches->get($drugId) ?? collect();

    if ($drugBatches->isEmpty()) {
        $skipped++;
        continue;
    }

    $drugSales      = $salesByDrug->get($drugId) ?? collect();
    $drugHistorical = $historicalByDrug->get($drugId) ?? collect();

    // === 8 Fitur ===

    // 1. Total masuk (historis)
    $totalMasuk = (int) $drugBatches->sum('stok_masuk');

    // 2. Total keluar (historis 12 bulan)
    $totalKeluar = (int) $drugHistorical->sum('stok_keluar');

    // 3. Stok saat ini
    $stokSaatIni = (int) $drugBatches->sum('stok_sisa');

    // 4. Frekuensi transaksi (3 bulan)
    $frekuensiTransaksi = $drugSales->count();
    $periodSalesTotal   = (int) $drugSales->sum('stok_keluar');

    // 5. Avg transaksi (3 bulan)
    $avgTransaksi = $frekuensiTransaksi > 0
        ? round($periodSalesTotal / $frekuensiTransaksi, 2)
        : 0;

    // 6. Safety stock
    $safetyStock = (int) ($drug->safety_stock ?? 10);

    // 7. Avg daily sales (3 bulan)
    $avgDailySales = round($periodSalesTotal / $periodDays, 4);

    // 8. Demand volatility (std deviasi harian, 3 bulan)
    $salesByDate = [];
    foreach ($drugSales as $sale) {
        $date = $sale->created_at->format('Y-m-d');
        $salesByDate[$date] = ($salesByDate[$date] ?? 0) + $sale->stok_keluar;
    }
    $dailyValues = [];
    $cur = $periodStart->copy();
    while ($cur <= $periodEnd) {
        $dailyValues[] = $salesByDate[$cur->format('Y-m-d')] ?? 0;
        $cur->addDay();
    }
    $mean     = count($dailyValues) > 0 ? array_sum($dailyValues) / count($dailyValues) : 0;
    $variance = 0;
    foreach ($dailyValues as $v) {
        $variance += pow($v - $mean, 2);
    }
    $demandVolatility = count($dailyValues) > 1
        ? round(sqrt($variance / count($dailyValues)), 4)
        : 0;

    // === TARGET VARIABLE ===
    // predicted_required_stock = Kebutuhan stok 1 bulan ke depan
    // Formula: avg_daily_sales × 30 + safety_stock + volatility_buffer
    $volatilityBuffer       = $demandVolatility * 1.5;
    $predictedRequiredStock = round(($avgDailySales * 30) + $safetyStock + $volatilityBuffer, 2);

    $rows[] = [
        'id_obat'                  => $drugId,
        'nama_obat'                => $drug->nama_obat,
        'total_masuk'              => $totalMasuk,
        'total_keluar'             => $totalKeluar,
        'stok_saat_ini'            => $stokSaatIni,
        'frekuensi_transaksi'      => $frekuensiTransaksi,
        'avg_transaksi'            => $avgTransaksi,
        'demand_volatility'        => $demandVolatility,
        'avg_daily_sales'          => $avgDailySales,
        'safety_stock'             => $safetyStock,
        'predicted_required_stock' => $predictedRequiredStock,  // TARGET
    ];
}

echo "Berhasil diproses: " . count($rows) . " obat\n";
echo "Dilewati (tidak ada batch): {$skipped} obat\n\n";

// ── Tulis ke CSV ──────────────────────────────────────────────────────────
if (empty($rows)) {
    echo "❌ Tidak ada data untuk diekspor! Pastikan ada transaksi obat keluar di database.\n";
    exit(1);
}

$fp = fopen($OUTPUT_PATH, 'w');
if (!$fp) {
    echo "❌ Gagal membuka file: {$OUTPUT_PATH}\n";
    exit(1);
}

// Header
fputcsv($fp, array_keys($rows[0]));

// Rows
foreach ($rows as $row) {
    fputcsv($fp, $row);
}
fclose($fp);

$fileSizeKb = round(filesize($OUTPUT_PATH) / 1024, 2);
echo "✅ File disimpan: {$OUTPUT_PATH}\n";
echo "   Ukuran file : {$fileSizeKb} KB\n";
echo "   Total baris : " . count($rows) . " obat\n\n";

echo "==========================================================\n";
echo " LANGKAH SELANJUTNYA:\n";
echo "==========================================================\n";
echo " 1. Copy file ke: backend/dataset/training_data_from_db.csv\n";
echo " 2. Jalankan training:\n";
echo "    cd backend\n";
echo "    python notebook/model_trainer.py\n";
echo "       (edit data_path di model_trainer.py ke training_data_from_db.csv)\n";
echo " 3. Copy model baru ke: apotek-stock-prediction/model/stock_prediction_model.pkl\n";
echo " 4. Push ke Hugging Face:\n";
echo "    cd apotek-stock-prediction && git add . && git commit -m 'Update model with real data' && git push\n";
echo "==========================================================\n";
