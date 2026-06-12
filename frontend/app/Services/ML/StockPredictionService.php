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
 * Orchestrates feature extraction and ML predictions
 * Provides hybrid mode: tries ML API, falls back to database logic
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
        $this->mlService = $mlService;
    }

    /**
     * Check if ML API is available
     */
    public function isAPIAvailable(): bool
    {
        return $this->mlService->isAPIAvailable();
    }

    /**
     * Get predictions for all drugs
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
     * Get prediction for single drug
     */
    public function getDrugPrediction(int $id_obat): ?array
    {
        try {
            $drug = Obat::find($id_obat);
            if (!$drug) {
                return null;
            }

            // Extract features
            $features = $this->featureService->extractFeatures($id_obat);
            if (!$features) {
                return null;
            }

            // Try ML prediction
            $prediction = null;
            if ($this->mlService->isAPIAvailable()) {
                $prediction = $this->mlService->predict($features);
            }

            // Fallback: calculate from database
            if (!$prediction) {
                $prediction = $this->calculateDatabasePrediction($features);
            }

            return array_merge($features, $prediction);
        } catch (\Exception $e) {
            Log::error("Prediction error for drug {$id_obat}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Compute all predictions
     */
    private function computeAllPredictions(): Collection
    {
        try {
            // ! OPTIMIZATION: Check API availability once
            $apiAvailable = $this->mlService->isAPIAvailable();
            
            $drugs = Obat::all();
            $allPredictions = collect();
            
            Log::info("Starting computeAllPredictions for " . $drugs->count() . " drugs. API Available: " . ($apiAvailable ? 'Yes' : 'No'));

            // ! OPTIMIZATION: Process in chunks of 100 to balance memory and speed
            $drugs->chunk(100)->each(function ($chunk, $index) use ($allPredictions, $apiAvailable) {
                try {
                    // 1. Extract features for the chunk in bulk
                    $featureBatch = $this->featureService->extractFeaturesForDrugs($chunk);
                    
                    if ($featureBatch->isEmpty()) {
                        Log::warning("Chunk {$index}: No features extracted for " . $chunk->count() . " drugs");
                        return;
                    }

                    Log::info("Chunk {$index}: Extracted features for " . $featureBatch->count() . " drugs");

                    $mlPredictions = [];
                    
                    // 2. Try ML batch prediction
                    if ($apiAvailable) {
                        $response = $this->mlService->predictBatch($featureBatch->toArray());
                        if ($response && isset($response['results'])) {
                            // Map ML results by id_obat
                            foreach ($response['results'] as $res) {
                                if (isset($res['id_obat'])) {
                                    $mlPredictions[$res['id_obat']] = $res;
                                }
                            }
                            Log::info("Chunk {$index}: Received " . count($mlPredictions) . " ML predictions");
                        } else {
                            Log::warning("Chunk {$index}: ML Batch prediction returned no results");
                        }
                    }
                    
                    // 3. Merge features with predictions (ML or Fallback)
                    $pushedCount = 0;
                    foreach ($featureBatch as $features) {
                        $id_obat = $features['id_obat'];

                        if (isset($mlPredictions[$id_obat]) && !isset($mlPredictions[$id_obat]['error'])) {
                            // Use ML prediction
                            $prediction = [
                                'predicted_stock' => $mlPredictions[$id_obat]['predicted_stock'],
                                'confidence' => $mlPredictions[$id_obat]['confidence'],
                                'recommendation' => $this->generateRecommendation($mlPredictions[$id_obat]['predicted_stock'], $features),
                                'source' => 'ml',
                            ];
                        } else {
                            // Fallback to database logic
                            $prediction = $this->calculateDatabasePrediction($features);
                        }

                        $merged = array_merge($features, $prediction);

                        // ! ADDED: Include status in the raw data
                        $merged['status'] = $this->getStockStatus(
                            (int)$merged['stok_saat_ini'], 
                            (int)$merged['safety_stock'],
                            (int)$merged['predicted_stock']
                        );

                        $allPredictions->push($merged);
                        $pushedCount++;
                    }
                    Log::info("Chunk {$index}: Pushed {$pushedCount} predictions to total");
                } catch (\Exception $e) {
                    Log::error("Error in chunk {$index}: " . $e->getMessage());
                    // Don't throw, just continue with next chunk to get at least some data
                }
            });

            Log::info("Finished computeAllPredictions. Total: " . $allPredictions->count() . " predictions");
            return $allPredictions;
        } catch (\Exception $e) {
            Log::error("computeAllPredictions failed: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return collect(); // Return empty instead of throwing to avoid 500
        }
    }


    /**
     * Helper to generate recommendation based on predicted stock (moved from ML backend logic)
     */
    private function generateRecommendation(float $predicted, array $features): string
    {
        $current = $features['stok_saat_ini'];
        $safety = $features['safety_stock'];
        $gap = $predicted - $current;

        if ($gap > $safety) {
            return "Pesanan mendesak {$gap} unit (Melebihi Safety Stock)";
        } elseif ($gap > 0) {
            return "Rekomendasi pesanan {$gap} unit untuk 1 bulan";
        } elseif ($current >= $predicted * 1.2) {
            return "Stok sangat mencukupi, tidak perlu pesan";
        } else {
            return "Stok mencukupi, pantau secara berkala";
        }
    }

    /**
     * Calculate prediction from database logic
     * Fallback when ML API is unavailable
     */
    private function calculateDatabasePrediction(array $features): array
    {
        $current_stock = $features['stok_saat_ini'];
        $avg_daily_sales = $features['avg_daily_sales'];
        $safety_stock = $features['safety_stock'];
        $demand_volatility = $features['demand_volatility'];
        $trend_indicator = $features['trend_indicator'] ?? 1.0;

        // Optimized logic: (avg_daily_sales × 30) + safety_stock + (volatility * safety_factor)
        // This ensures we cover average demand plus peak demand fluctuations
        $base_prediction = ($avg_daily_sales * 30) + $safety_stock + ($demand_volatility * 1.5);
        
        // Apply trend factor if sales are increasing
        if ($trend_indicator > 1.1) {
            $base_prediction *= min(1.5, $trend_indicator);
        }

        $predicted_stock = round($base_prediction, 0);

        // Determine confidence based on data quality
        $confidence = 'medium';
        if ($features['frekuensi_transaksi'] > 20) {
            $confidence = 'high';
        } elseif ($features['frekuensi_transaksi'] < 5) {
            $confidence = 'low';
        }

        // Generate recommendation
        $gap = $predicted_stock - $current_stock;
        if ($gap > $safety_stock) {
            $recommendation = "⚠️ Pesanan mendesak {$gap} unit (Permintaan Tinggi)";
        } elseif ($gap > 0) {
            $recommendation = "Rekomendasi pesanan {$gap} unit untuk 1 bulan";
        } else {
            $recommendation = "Stok saat ini masih mencukupi";
        }

        return [
            'predicted_stock' => (float)$predicted_stock,
            'confidence' => $confidence,
            'recommendation' => $recommendation,
            'source' => 'database',
        ];
    }

    /**
     * Get stock status based on current stock, safety stock, and predicted demand
     */
    public function getStockStatus(int $current_stock, int $safety_stock, int $predicted_stock = 0): string
    {
        if ($current_stock <= 0) {
            return 'habis';
        }
        
        // If current stock is below safety stock or below 30% of predicted monthly demand
        if ($current_stock <= $safety_stock || ($predicted_stock > 0 && $current_stock < ($predicted_stock * 0.3))) {
            return 'menipis';
        }
        
        return 'aman';
    }

    /**
     * Get last month sales summary for a drug
     */
    public function getLastMonthSummary(int $id_obat): ?array
    {
        return $this->featureService->getLastMonthSalesSummary($id_obat);
    }

    /**
     * Clear prediction cache
     */
    public function clearCache(): void
    {
        Cache::forget('stock_predictions_all');
        Log::info("Stock prediction cache cleared");
    }

    /**
     * Get prediction statistics
     */
    public function getPredictionStats(): array
    {
        try {
            $predictions = $this->getAllPredictions();

            if ($predictions->isEmpty()) {
                return [
                    'total_drugs' => 0,
                    'status_aman' => 0,
                    'status_menipis' => 0,
                    'status_habis' => 0,
                    'total_reorder_value' => 0,
                    'api_available' => $this->mlService->isAPIAvailable(),
                ];
            }

            return [
                'total_drugs' => $predictions->count(),
                'status_aman' => $predictions->filter(fn($p) => ($p['status'] ?? '') === 'aman')->count(),
                'status_menipis' => $predictions->filter(fn($p) => ($p['status'] ?? '') === 'menipis')->count(),
                'status_habis' => $predictions->filter(fn($p) => ($p['status'] ?? '') === 'habis')->count(),
                'total_reorder_value' => (float)$predictions->sum(function ($p) {
                    $stok_saat_ini = $p['stok_saat_ini'] ?? 0;
                    $predicted = $p['predicted_stock'] ?? $stok_saat_ini;
                    $gap = $predicted - $stok_saat_ini;
                    return max(0, $gap);
                }),
                'api_available' => $this->mlService->isAPIAvailable(),
            ];
        } catch (\Exception $e) {
            \Log::error("getPredictionStats error: " . $e->getMessage());
            return [
                'total_drugs' => 0,
                'status_aman' => 0,
                'status_menipis' => 0,
                'status_habis' => 0,
                'total_reorder_value' => 0,
                'api_available' => $this->mlService->isAPIAvailable(),
            ];
        }
    }
}
