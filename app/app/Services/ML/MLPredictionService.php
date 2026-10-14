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
    private string $api_url;
    private int $api_timeout;
    private ?bool $is_available = null;

    public function __construct()
    {
        // Ambil dari .env → ML_API_URL dan ML_API_TIMEOUT
        $this->api_url     = rtrim(env('ML_API_URL', 'http://localhost:5000'), '/');
        $this->api_timeout = (int) env('ML_API_TIMEOUT', 30);
    }

    /**
     * Check if ML API is available (with static cache per request)
     */
    public function isAPIAvailable(): bool
    {
        if ($this->is_available !== null) {
            return $this->is_available;
        }

        try {
            // HF Spaces bisa ada cold start, gunakan timeout yang cukup
            $response = Http::timeout(10)->get("{$this->api_url}/status");
            $this->is_available = $response->successful() && $response->json('status') === 'healthy';
            return $this->is_available;
        } catch (\Exception $e) {
            $this->is_available = false;
            return false;
        }
    }

    /**
     * Get prediction for a single drug
     *
     * @param array $features Array fitur dari StockFeatureService
     * @return array|null  Array berisi predicted_stock, confidence, rekomendasi_order
     */
    public function predict(array $features): ?array
    {
        try {
            // 8 fitur inti yang digunakan model Decision Tree Regressor
            // Urutan harus sama dengan feature_names saat model dilatih
            $required_features = [
                'total_masuk',
                'total_keluar',
                'stok_saat_ini',
                'frekuensi_transaksi',
                'avg_transaksi',
                'demand_volatility',
                'avg_daily_sales',
                'safety_stock',
            ];

            // Bangun payload hanya dengan fitur yang diperlukan
            $payload = [];
            foreach ($required_features as $feature) {
                if (!array_key_exists($feature, $features)) {
                    Log::warning("Feature tidak tersedia: {$feature} — menggunakan 0");
                    $payload[$feature] = 0;
                } else {
                    $payload[$feature] = $features[$feature];
                }
            }
            // Sertakan stok_saat_ini untuk kalkulasi rekomendasi_order di Flask
            $payload['stok_saat_ini'] = $features['stok_saat_ini'] ?? 0;

            $response = Http::timeout($this->api_timeout)
                ->post("{$this->api_url}/predict", $payload);

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
     * Get batch predictions untuk banyak obat sekaligus
     * Hanya mengirim 8 fitur inti + id_obat + stok_saat_ini ke API
     *
     * @param array $predictions_list Array of feature sets dari StockFeatureService
     * @return array|null
     */
    public function predictBatch(array $predictions_list): ?array
    {
        // 8 fitur yang dikirim ke model
        $required_features = [
            'total_masuk', 'total_keluar', 'stok_saat_ini',
            'frekuensi_transaksi', 'avg_transaksi',
            'demand_volatility', 'avg_daily_sales', 'safety_stock',
        ];

        try {
            // Filter setiap item agar hanya mengandung 8 fitur + id_obat
            $filtered = array_map(function ($item) use ($required_features) {
                $payload = ['id_obat' => $item['id_obat'] ?? null];
                foreach ($required_features as $f) {
                    $payload[$f] = $item[$f] ?? 0;
                }
                // Sertakan stok_saat_ini untuk kalkulasi rekomendasi_order di Flask
                $payload['stok_saat_ini'] = $item['stok_saat_ini'] ?? 0;
                return $payload;
            }, $predictions_list);

            $response = Http::timeout($this->api_timeout * 2)
                ->post("{$this->api_url}/predict/batch", [
                    'predictions' => $filtered
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
