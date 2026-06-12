<?php

namespace App\Providers;

use App\Services\ML\StockFeatureService;
use App\Services\ML\MLPredictionService;
use App\Services\ML\StockPredictionService;
use App\Services\ML\DataExportService;
use Illuminate\Support\ServiceProvider;

/**
 * ML Services Provider
 *
 * Registers all ML-related services into the container
 */
class MLServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Check if ML API should be enabled (can be disabled via env)
        $apiEnabled = env('ML_API_ENABLED', true);

        // Register StockFeatureService as singleton
        $this->app->singleton(StockFeatureService::class, function ($app) {
            return new StockFeatureService();
        });

        // Register MLPredictionService as singleton
        $this->app->singleton(MLPredictionService::class, function ($app) use ($apiEnabled) {
            $service = new MLPredictionService();
            if (!$apiEnabled) {
                $service->disableAPI();
            }
            return $service;
        });

        // Register StockPredictionService with dependencies
        $this->app->singleton(StockPredictionService::class, function ($app) {
            return new StockPredictionService(
                $app->make(StockFeatureService::class),
                $app->make(MLPredictionService::class)
            );
        });

        // Register DataExportService with dependencies
        $this->app->singleton(DataExportService::class, function ($app) {
            return new DataExportService(
                $app->make(StockFeatureService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
