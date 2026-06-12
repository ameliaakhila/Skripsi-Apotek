<?php

namespace App\Services\ML;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ML Prediction API Service
 *
 * Communicates with Flask REST API for stock predictions
 */
class MLPredictionService
{
    private $api_url = 'http://localhost:5000';
    private $api_timeout = 2; // Kurangi timeout agar fallback lebih cepat
    private $is_available = null;

    /**
     * Check if ML API is available (with static cache per request)
     */
    public function isAPIAvailable(): bool
    {
        if ($this->is_available !== null) {
            return $this->is_available;
        }

        try {
            // Gunakan timeout yang sangat singkat untuk cek status
            $response = Http::timeout(1)->get("{$this->api_url}/status");
            $this->is_available = $response->successful();
            return $this->is_available;
        } catch (\Exception $e) {
            $this->is_available = false;
            return false;
        }
    }

    /**
     * Get prediction for a single drug
     *
     * @param array $features 12 features for prediction
     * @return array|null
     */
    public function predict(array $features): ?array
    {
        try {
            // Validate required features
            $required_features = [
                'total_masuk', 'total_keluar', 'stok_saat_ini', 'frekuensi_transaksi',
                'avg_transaksi', 'kedaluwarsa_risk', 'accuracy_deviation', 'safety_stock_ratio',
                'demand_volatility', 'trend_indicator', 'safety_stock', 'avg_daily_sales'
            ];

            foreach ($required_features as $feature) {
                if (!isset($features[$feature])) {
                    Log::error("Missing feature: {$feature}");
                    return null;
                }
            }

            $response = Http::timeout($this->api_timeout)
                ->post("{$this->api_url}/predict", $features);

            if (!$response->successful()) {
                Log::error("API prediction failed: {$response->status()}");
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("ML prediction error: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Get batch predictions for multiple drugs
     *
     * @param array $predictions_list Array of feature sets
     * @return array|null
     */
    public function predictBatch(array $predictions_list): ?array
    {
        try {
            $response = Http::timeout($this->api_timeout * 2)
                ->post("{$this->api_url}/predict/batch", [
                    'predictions' => $predictions_list
                ]);

            if (!$response->successful()) {
                Log::error("API batch prediction failed: {$response->status()}");
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("ML batch prediction error: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Get model information
     */
    public function getModelInfo(): ?array
    {
        try {
            $response = Http::timeout($this->api_timeout)
                ->get("{$this->api_url}/model/info");

            if (!$response->successful()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set API URL (for testing/configuration)
     */
    public function setAPIUrl(string $url): void
    {
        $this->api_url = $url;
    }

    /**
     * Set API timeout
     */
    public function setAPITimeout(int $seconds): void
    {
        $this->api_timeout = $seconds;
    }
}
