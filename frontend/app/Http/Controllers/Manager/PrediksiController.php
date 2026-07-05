<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\ML\StockPredictionService;
use App\Models\Admin\StokBatch;
use App\Exports\PrediksiObatExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Prediksi Stok Obat Controller
 *
 * Mengelola prediksi stok obat menggunakan ML model dan data penjualan bulanan
 */
class PrediksiController extends Controller
{
    private $predictionService;

    public function __construct(StockPredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    /**
     * Tampilkan halaman prediksi stok obat
     */
    public function index()
    {
        try {
            // Get predictions (with cache)
            $data = $this->predictionService->getAllPredictions(use_cache: true);

            // Get statistics (Optimized: Get stats directly from the data we already have)
            $stats = $this->calculateStatsFromData($data);

            // Format data for view
            $formattedData = $data->map(function ($prediction) {
                // Ensure $prediction is handled as array
                $predictionArray = is_array($prediction) ? $prediction : $prediction->toArray();
                return $this->formatPredictionData($predictionArray);
            });

            return view('manager.prediksi-obat', [
                'data' => $formattedData,
                'stats' => $stats,
                'api_available' => $stats['api_available'] ?? false,
            ]);
        } catch (\Exception $e) {
            Log::error("Prediksi index error: {$e->getMessage()}");
            return view('manager.prediksi-obat', [
                'data' => collect(),
                'stats' => [
                    'total_drugs' => 0,
                    'status_aman' => 0,
                    'status_menipis' => 0,
                    'status_habis' => 0,
                    'api_available' => false,
                ],
                'error' => 'Gagal memuat data prediksi: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Calculate statistics from already fetched data to avoid double processing
     */
    private function calculateStatsFromData($predictions): array
    {
        if ($predictions->isEmpty()) {
            return [
                'total_drugs' => 0,
                'status_aman' => 0,
                'status_menipis' => 0,
                'status_habis' => 0,
                'total_reorder_value' => 0,
                'api_available' => $this->predictionService->isAPIAvailable(),
            ];
        }

        return [
            'total_drugs' => $predictions->count(),
            'status_aman' => $predictions->filter(fn($p) => (($p['status'] ?? '') === 'aman'))->count(),
            'status_menipis' => $predictions->filter(fn($p) => (($p['status'] ?? '') === 'menipis'))->count(),
            'status_habis' => $predictions->filter(fn($p) => (($p['status'] ?? '') === 'habis'))->count(),
            'total_reorder_value' => (float)$predictions->sum(function ($p) {
                $stok_saat_ini = $p['stok_saat_ini'] ?? 0;
                $predicted = $p['predicted_stock'] ?? $stok_saat_ini;
                $gap = $predicted - $stok_saat_ini;
                return max(0, $gap);
            }),
            'api_available' => $this->predictionService->isAPIAvailable(),
        ];
    }

    /**
     * Generate prediksi stok berdasarkan data penjualan bulan lalu
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generatePrediction(Request $request)
    {
        try {
            // Hapus cache dan force regenerate
            $this->predictionService->clearCache();
            
            // Force recompute and cache immediately (This might take a few seconds)
            // This ensures data is ready when user is redirected back to index
            $this->predictionService->getAllPredictions(use_cache: true);

            Log::info("Stock prediction regenerated and cached at " . now());

            return redirect()->back()
                ->with('success', 'Prediksi stok berhasil diperbarui berdasarkan riwayat transaksi 3 bulan terakhir');
        } catch (\Exception $e) {
            Log::error("Generate prediction error: {$e->getMessage()}");

            return redirect()->back()
                ->with('error', 'Gagal membuat prediksi: ' . $e->getMessage());
        }
    }

    /**
     * Get prediction untuk obat spesifik (AJAX)
     */
    public function getDrugPrediction($id_obat)
    {
        try {
            $prediction = $this->predictionService->getDrugPrediction($id_obat);

            if (!$prediction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatPredictionData($prediction),
            ]);
        } catch (\Exception $e) {
            Log::error("Get drug prediction error: {$e->getMessage()}");

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ringkasan penjualan bulan lalu (AJAX)
     */
    public function getLastMonthSummary($id_obat)
    {
        try {
            $summary = $this->predictionService->getLastMonthSummary($id_obat);

            if (!$summary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            Log::error("Get last month summary error: {$e->getMessage()}");

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format prediction data for view display
     */
    private function formatPredictionData(array $prediction): array
    {
        $current_stock   = $prediction['stok_saat_ini'] ?? 0;
        $predicted_stock = $prediction['predicted_stock'] ?? $current_stock;
        $safety_stock    = $prediction['safety_stock'] ?? 10;
        $avg_daily_sales = $prediction['avg_daily_sales'] ?? 0;

        // Total penjualan 3 bulan terakhir (alias kompatibilitas)
        $sales_3month  = (int) ($prediction['last_3month_total_sales'] ?? $prediction['last_month_total_sales'] ?? 0);

        // Rekomendasi order = berapa yang harus dipesan agar stok cukup 1 bulan
        $rekomendasi_order = (int) ($prediction['rekomendasi_order'] ?? max(0, $predicted_stock - $current_stock));

        return [
            'id_obat'               => $prediction['id_obat'] ?? null,
            'nama_obat'             => $prediction['nama_obat'] ?? 'Unknown',
            // Stok awal = stok saat ini + penjualan 3 bulan (estimasi stok sebelum periode ini)
            'stok_awal'             => (int) ($current_stock + $sales_3month),
            'stok_terjual'          => $sales_3month,
            'stok_sisa'             => (int) $current_stock,
            // Kebutuhan 1 bulan ke depan (hasil prediksi model)
            'stok_prediksi'         => (int) $predicted_stock,
            'safety_stock'          => (int) $safety_stock,
            'confidence'            => $prediction['confidence'] ?? 'medium',
            'recommendation'        => $prediction['rekomendasi_order_text'] ?? $prediction['recommendation'] ?? '-',
            'status'                => $this->predictionService->getStockStatus(
                                            (int) $current_stock,
                                            (int) $safety_stock,
                                            (int) $predicted_stock
                                        ),
            // Berapa unit yang harus dipesan sekarang
            'rekomendasi_order'     => $rekomendasi_order,
            // Rata-rata penjualan harian berdasarkan 3 bulan terakhir
            'avg_daily_sales'       => round((float) $avg_daily_sales, 1),
            // Informasi periode data
            'periode_data'          => $prediction['periode_data'] ?? '3 bulan terakhir',
            'source'                => $prediction['source'] ?? 'ml',
            'is_prediction'         => true,
        ];
    }

    public function upload(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $data = [];

        // Baca file CSV
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        return view('manager.prediksi-obat', compact('data'));
    }

    public function obat_kedaluwarsa()
    {
        // ! OPTIMIZATION: Cache expired/near-expiry medicine data (3600s = 1 hour)
        $cacheKey = 'obat_kedaluwarsa_report';
        $data = Cache::remember($cacheKey, 3600, function () {
            // Query actual expired/near-expiry medicine data from database
            // We focus on batches that still have stock (stok_sisa > 0)
            return StokBatch::with('Obat')
                ->where('tanggal_kedaluwarsa', '<=', now()->addMonths(3)->format('Y-m-d'))
                ->orderBy('tanggal_kedaluwarsa', 'asc')
                ->get()
                ->map(function ($stok) {
                    $expiry_date = $stok->tanggal_kedaluwarsa;

                    // Logic for status_terjual based on remaining stock
                    $status_terjual = $stok->stok_sisa == 0 ? 'Terjual' : 'Belum Terjual';

                    // Determine condition based on expiry date
                    $kondisi = $expiry_date->isPast()
                        ? 'Sudah Kadaluarsa'
                        : ($expiry_date->diffInDays(now()) <= 30 ? 'Menipis Baik' : 'Baik');

                    return [
                        'nama_obat' => $stok->Obat->nama_obat ?? 'Tidak Diketahui',
                        'jumlah_obat' => $stok->stok_sisa,
                        'tanggal_kedaluwarsa' => $expiry_date->format('d F Y'),
                        'status_terjual' => $status_terjual,
                        'kondisi' => $kondisi
                    ];
                });
        });

        // Fallback: If no expiry data, show empty collection
        if ($data->isEmpty()) {
            $data = collect([]);
        }

        return view('manager.obat-kedaluwarsa', compact('data'));
    }

    /**
     * Export prediksi obat ke Excel
     */
    public function exportExcel()
    {
        try {
            $data = $this->predictionService->getAllPredictions(use_cache: true);

            $formattedData = $data->map(function ($prediction) {
                return $this->formatPredictionData($prediction);
            })->toArray();

            return Excel::download(
                new PrediksiObatExport($formattedData),
                'Prediksi_Stok_Obat_' . now()->format('d-m-Y') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error("Export prediksi Excel error: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export prediksi obat ke PDF (Tampilkan di browser, user bisa print)
     */
    public function exportPdf()
    {
        try {
            $data = $this->predictionService->getAllPredictions(use_cache: true);

            $formattedData = $data->map(function ($prediction) {
                return $this->formatPredictionData($prediction);
            });

            $stats = $this->calculateStatsFromData($data);

            return view('manager.prediksi-obat-pdf', [
                'data' => $formattedData,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error("Export prediksi PDF error: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Gagal membuka preview PDF: ' . $e->getMessage());
        }
    }
}
