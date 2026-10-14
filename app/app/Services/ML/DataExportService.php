<?php

namespace App\Services\ML;

use App\Models\Admin\Obat;
use App\Models\Admin\StokBatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * ML Data Export Service
 *
 * Exports stock data with extracted features for ML model training
 * Uses StockFeatureService for consistent feature extraction
 */
class DataExportService
{
    private $featureService;
    private $storagePath = 'training_data_prediksi_stok';

    public function __construct(StockFeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    /**
     * Export features for all drugs to CSV
     *
     * @param int $months Historical period
     * @param string|null $filename Optional custom filename
     * @return string Path to exported file
     */
    public function exportToCSV(int $months = 12, ?string $filename = null): string
    {
        try {
            // Extract features for all drugs
            $features = $this->featureService->extractAllFeatures($months);

            if ($features->isEmpty()) {
                throw new \Exception("No features extracted");
            }

            // Generate filename
            if (!$filename) {
                $filename = "training_data_" . now()->format('Y-m-d_His') . ".csv";
            }

            // Create CSV content
            $csvContent = $this->generateCSVContent($features);

            // Store file
            $path = $this->storagePath . '/' . $filename;
            Storage::disk('local')->put($path, $csvContent);

            return Storage::path($path);
        } catch (\Exception $e) {
            \Log::error("Data export error: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Generate CSV content from features
     */
    private function generateCSVContent(Collection $features): string
    {
        // CSV header
        $headers = [
            'id_obat',
            'nama_obat',
            'total_masuk',
            'total_keluar',
            'stok_saat_ini',
            'frekuensi_transaksi',
            'avg_transaksi',
            'kedaluwarsa_risk',
            'accuracy_deviation',
            'safety_stock_ratio',
            'demand_volatility',
            'trend_indicator',
            'safety_stock',
            'avg_daily_sales',
            'last_month_total_sales'
        ];

        $csv = implode(',', $headers) . "\n";

        // Add data rows
        foreach ($features as $feature) {
            $row = [
                $feature['id_obat'] ?? '',
                '"' . addslashes($feature['nama_obat'] ?? '') . '"',
                $feature['total_masuk'] ?? 0,
                $feature['total_keluar'] ?? 0,
                $feature['stok_saat_ini'] ?? 0,
                $feature['frekuensi_transaksi'] ?? 0,
                $feature['avg_transaksi'] ?? 0,
                $feature['kedaluwarsa_risk'] ?? 0,
                $feature['accuracy_deviation'] ?? 0,
                $feature['safety_stock_ratio'] ?? 0,
                $feature['demand_volatility'] ?? 0,
                $feature['trend_indicator'] ?? 0,
                $feature['safety_stock'] ?? 0,
                $feature['avg_daily_sales'] ?? 0,
                $feature['last_month_total_sales'] ?? 0,
            ];

            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }

    /**
     * Get list of exported files
     */
    public function getExportedFiles(): Collection
    {
        try {
            $files = Storage::disk('local')->files($this->storagePath);
            return collect($files)->map(fn($file) => [
                'name' => basename($file),
                'path' => $file,
                'size' => Storage::disk('local')->size($file),
                'date' => Storage::disk('local')->lastModified($file),
            ])->sortByDesc('date');
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Delete exported file
     */
    public function deleteExportedFile(string $filename): bool
    {
        try {
            $path = $this->storagePath . '/' . $filename;
            return Storage::disk('local')->delete($path);
        } catch (\Exception $e) {
            \Log::error("Delete export error: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Get latest exported file
     */
    public function getLatestExportedFile(): ?array
    {
        $files = $this->getExportedFiles();
        return $files->first();
    }
}
