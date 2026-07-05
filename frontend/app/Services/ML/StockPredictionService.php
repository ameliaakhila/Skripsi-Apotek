<?php

namespace App\Services\ML;

use App\Models\Admin\Obat;
use App\Models\Admin\StokBatch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

/**
 * Stock Prediction Service
 *
 * Mengorkestrasi feature extraction dan ML predictions.
 * Mode hybrid: coba ML API, fallback ke logika database.
 *
 * Makna "predicted_stock":
 *   = Kebutuhan stok untuk 1 bulan ke depan
 *   = (avg_daily_sales × 30) + safety_stock buffer
 *   Berdasarkan rata-rata penjualan 3 BULAN TERAKHIR.
 *
 * "rekomendasi_order":
 *   = max(0, predicted_stock - stok_saat_ini)
 *   = Berapa unit yang perlu dipesan agar stok aman 1 bulan
 */
class StockPredictionService
{
    private $featureService;
    private $mlService;
    private $cachedPredictions = null;

    public function __construct(
        StockFeatureService $featureService,
        MLPredictionService $mlService
    ) {
        $this->featureService = $featureService;
        $this->mlService      = $mlService;
    }

    /**
     * Check if ML API is available
     */
    public function isAPIAvailable(): bool
    {
        return $this->mlService->isAPIAvailable();
    }

    /**
     * Get predictions untuk semua obat
     *
     * @param bool $use_cache Cache results
     * @return Collection
     */
    public function getAllPredictions(bool $use_cache = true): Collection
    {
        if ($this->cachedPredictions !== null) {
            return $this->cachedPredictions;
        }

        if ($use_cache) {
            $this->cachedPredictions = Cache::remember('stock_predictions_all', now()->addHours(1), function () {
                return $this->computeAllPredictions();
            });
            return $this->cachedPredictions;
        }

        $this->cachedPredictions = $this->computeAllPredictions();
        return $this->cachedPredictions;
    }

    /**
     * Get prediction untuk satu obat
     */
    public function getDrugPrediction(int $id_obat): ?array
    {
        try {
            $drug = Obat::find($id_obat);
            if (!$drug) {
                return null;
            }

            $features = $this->featureService->extractFeatures($id_obat);
            if (!$features) {
                return null;
            }

            $prediction = null;
            if ($this->mlService->isAPIAvailable()) {
                $prediction = $this->mlService->predict($features);
            }

            if (!$prediction) {
                $prediction = $this->calculateDatabasePrediction($features);
            }

            // Tambah rekomendasi_order
            $predicted = $prediction['predicted_stock'] ?? 0;
            $prediction['rekomendasi_order'] = max(0, $predicted - $features['stok_saat_ini']);
            $prediction['rekomendasi_order_text'] = $this->generateRecommendation($predicted, $features);

            return array_merge($features, $prediction);
        } catch (\Exception $e) {
            Log::error("Prediction error for drug {$id_obat}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Compute semua prediksi (core logic)
     */
    private function computeAllPredictions(): Collection
    {
        try {
            $apiAvailable  = $this->mlService->isAPIAvailable();
            $drugs         = Obat::all();
            $allPredictions = collect();

            Log::info("computeAllPredictions: " . $drugs->count() . " obat. API: " . ($apiAvailable ? 'Ya' : 'Tidak'));

            $drugs->chunk(100)->each(function ($chunk, $index) use ($allPredictions, $apiAvailable) {
                try {
                    // 1. Extract features (bulk)
                    $featureBatch = $this->featureService->extractFeaturesForDrugs($chunk);

                    if ($featureBatch->isEmpty()) {
                        Log::warning("Chunk {$index}: Tidak ada fitur untuk " . $chunk->count() . " obat");
                        return;
                    }

                    $mlPredictions = [];

                    // 2. Coba ML batch prediction
                    if ($apiAvailable) {
                        $response = $this->mlService->predictBatch($featureBatch->toArray());
                        if ($response && isset($response['results'])) {
                            foreach ($response['results'] as $res) {
                                if (isset($res['id_obat'])) {
                                    $mlPredictions[$res['id_obat']] = $res;
                                }
                            }
                            Log::info("Chunk {$index}: {" . count($mlPredictions) . "} prediksi ML diterima");
                        } else {
                            Log::warning("Chunk {$index}: ML batch tidak mengembalikan hasil");
                        }
                    }

                    // 3. Merge fitur dengan prediksi (ML atau fallback)
                    foreach ($featureBatch as $features) {
                        $id_obat = $features['id_obat'];

                        if (isset($mlPredictions[$id_obat]) && !isset($mlPredictions[$id_obat]['error'])) {
                            $predicted = $mlPredictions[$id_obat]['predicted_stock'];
                            $prediction = [
                                'predicted_stock'       => $predicted,
                                'confidence'            => $mlPredictions[$id_obat]['confidence'],
                                'rekomendasi_order'     => max(0, $predicted - $features['stok_saat_ini']),
                                'rekomendasi_order_text' => $this->generateRecommendation($predicted, $features),
                                'source'                => 'ml',
                            ];
                        } else {
                            // Fallback ke logika database
                            $prediction = $this->calculateDatabasePrediction($features);
                        }

                        $merged = array_merge($features, $prediction);

                        // Hitung status stok
                        $merged['status'] = $this->getStockStatus(
                            (int) $merged['stok_saat_ini'],
                            (int) $merged['safety_stock'],
                            (int) $merged['predicted_stock']
                        );

                        $allPredictions->push($merged);
                    }
                } catch (\Exception $e) {
                    Log::error("Error di chunk {$index}: " . $e->getMessage());
                }
            });

            Log::info("computeAllPredictions selesai. Total: " . $allPredictions->count());
            return $allPredictions;
        } catch (\Exception $e) {
            Log::error("computeAllPredictions gagal: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return collect();
        }
    }

    /**
     * Hitung prediksi dari logika database (fallback ketika ML API tidak tersedia)
     *
     * Hasil = Kebutuhan stok untuk 1 bulan ke depan:
     *   base = (avg_daily_sales × 30) + safety_stock
     *   Jika tren naik → kalikan dengan faktor tren (max 1.5)
     */
    private function calculateDatabasePrediction(array $features): array
    {
        $avgDailySales   = max(0, $features['avg_daily_sales']);
        $safetyStock     = max(1, $features['safety_stock']);
        $stokSaatIni     = $features['stok_saat_ini'];
        $demandVolatility = $features['demand_volatility'];
        $trendIndicator  = $features['trend_indicator'] ?? 1.0;

        // Kebutuhan stok 1 bulan = rata-rata harian × 30 + buffer keamanan
        // Buffer keamanan = safety_stock + kompensasi volatilitas
        $volatilityBuffer = $demandVolatility * 1.5;
        $basePrediction   = ($avgDailySales * 30) + $safetyStock + $volatilityBuffer;

        // Koreksi tren naik (lebih dari 10%)
        if ($trendIndicator > 1.1) {
            $basePrediction *= min(1.5, $trendIndicator);
        }

        $predictedStock = (float) max(0, round($basePrediction, 0));

        // Rekomendasi order = berapa yang harus dipesan sekarang
        $rekomendasiOrder = max(0, $predictedStock - $stokSaatIni);

        // Tingkat kepercayaan berdasarkan kelengkapan data
        $frekuensi = $features['frekuensi_transaksi'];
        if ($frekuensi >= 20) {
            $confidence = 'high';
        } elseif ($frekuensi >= 5) {
            $confidence = 'medium';
        } else {
            $confidence = 'low';
        }

        return [
            'predicted_stock'        => $predictedStock,
            'confidence'             => $confidence,
            'rekomendasi_order'      => (float) $rekomendasiOrder,
            'rekomendasi_order_text' => $this->generateRecommendation($predictedStock, $features),
            'source'                 => 'database',
        ];
    }

    /**
     * Generate teks rekomendasi yang informatif
     */
    private function generateRecommendation(float $predicted, array $features): string
    {
        $current     = $features['stok_saat_ini'];
        $safety      = $features['safety_stock'];
        $gap         = $predicted - $current;
        $avgDaily    = $features['avg_daily_sales'];

        if ($gap > $safety * 2) {
            return "⚠️ Segera pesan {$gap} unit — stok tidak cukup untuk 1 bulan";
        } elseif ($gap > 0) {
            $daysLeft = $avgDaily > 0 ? round($current / $avgDaily) : 0;
            return "Pesan {$gap} unit (stok saat ini cukup ~{$daysLeft} hari)";
        } elseif ($current >= $predicted * 1.2) {
            return "✅ Stok sangat mencukupi, tidak perlu pesan bulan ini";
        } else {
            return "Stok cukup untuk 1 bulan, pantau berkala";
        }
    }

    /**
     * Tentukan status stok berdasarkan stok saat ini vs safety stock vs prediksi demand
     *
     * - 'habis'   : stok = 0
     * - 'menipis' : stok ≤ safety_stock ATAU stok < 30% dari kebutuhan 1 bulan
     * - 'aman'    : stok cukup untuk memenuhi perkiraan permintaan
     */
    public function getStockStatus(int $current_stock, int $safety_stock, int $predicted_stock = 0): string
    {
        if ($current_stock <= 0) {
            return 'habis';
        }

        if ($current_stock <= $safety_stock) {
            return 'menipis';
        }

        if ($predicted_stock > 0 && $current_stock < ($predicted_stock * 0.3)) {
            return 'menipis';
        }

        return 'aman';
    }

    /**
     * Get ringkasan penjualan 3 bulan terakhir untuk satu obat
     */
    public function getLastMonthSummary(int $id_obat): ?array
    {
        return $this->featureService->getLastMonthSalesSummary($id_obat);
    }

    /**
     * Hapus cache prediksi
     */
    public function clearCache(): void
    {
        Cache::forget('stock_predictions_all');
        $this->cachedPredictions = null;
        Log::info("Stock prediction cache dibersihkan");
    }

    /**
     * Get statistik prediksi
     */
    public function getPredictionStats(): array
    {
        try {
            $predictions = $this->getAllPredictions();

            if ($predictions->isEmpty()) {
                return [
                    'total_drugs'        => 0,
                    'status_aman'        => 0,
                    'status_menipis'     => 0,
                    'status_habis'       => 0,
                    'total_reorder_value' => 0,
                    'api_available'      => $this->mlService->isAPIAvailable(),
                ];
            }

            return [
                'total_drugs'        => $predictions->count(),
                'status_aman'        => $predictions->filter(fn($p) => ($p['status'] ?? '') === 'aman')->count(),
                'status_menipis'     => $predictions->filter(fn($p) => ($p['status'] ?? '') === 'menipis')->count(),
                'status_habis'       => $predictions->filter(fn($p) => ($p['status'] ?? '') === 'habis')->count(),
                'total_reorder_value' => (float) $predictions->sum(fn($p) => max(0, $p['rekomendasi_order'] ?? 0)),
                'api_available'      => $this->mlService->isAPIAvailable(),
            ];
        } catch (\Exception $e) {
            Log::error("getPredictionStats error: " . $e->getMessage());
            return [
                'total_drugs'        => 0,
                'status_aman'        => 0,
                'status_menipis'     => 0,
                'status_habis'       => 0,
                'total_reorder_value' => 0,
                'api_available'      => $this->mlService->isAPIAvailable(),
            ];
        }
    }
}
