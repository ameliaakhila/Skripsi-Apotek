<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ! Gzip compression middleware (before cache headers)
        $middleware->append(\App\Http\Middleware\CompressResponse::class);

        // ! Cache headers for static assets
        $middleware->append(\App\Http\Middleware\SetCacheHeaders::class);

        // Register middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'admin' => \App\Http\Middleware\CheckAdmin::class,
            'apoteker' => \App\Http\Middleware\CheckApoteker::class,
            'manager' => \App\Http\Middleware\CheckManager::class,
        ]);
    })
    ->withProviders([
        // \App\Providers\MLServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        // Custom exception handling
        // Error pages are automatically rendered from resources/views/errors/{status_code}.blade.php
    })->create();
