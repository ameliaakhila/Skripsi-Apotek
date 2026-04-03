<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Get the request path
        $path = $request->path();

        // Set cache headers based on asset type
        if ($this->isStaticAsset($path)) {
            // Cache static assets for 1 year (max-age)
            $response->header('Cache-Control', 'public, max-age=31536000, immutable');
            $response->header('Expires', gmdate('D, d M Y H:i:s T', time() + 31536000));
        } elseif ($this->isFont($path)) {
            // Cache fonts for 1 year
            $response->header('Cache-Control', 'public, max-age=31536000, immutable');
        } elseif ($this->isImage($path)) {
            // Cache images for 30 days
            $response->header('Cache-Control', 'public, max-age=2592000');
        } else {
            // Dynamic content - no cache
            $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', '0');
        }

        // Add compression headers if supported
        if ($this->acceptsGzip($request)) {
            $response->header('Vary', 'Accept-Encoding');
        }

        return $response;
    }

    /**
     * Check if the path is a static asset with hash (Vite/Laravel Mix)
     */
    private function isStaticAsset(string $path): bool
    {
        return preg_match('/\.(js|css)$/', $path) === 1 &&
            preg_match('/[a-f0-9]{8}/', $path) === 1;
    }

    /**
     * Check if the path is a font file
     */
    private function isFont(string $path): bool
    {
        return preg_match('/\.(woff|woff2|ttf|eot|otf)$/', $path) === 1;
    }

    /**
     * Check if the path is an image file
     */
    private function isImage(string $path): bool
    {
        return preg_match('/\.(jpg|jpeg|png|gif|webp|svg|ico|png)$/', $path) === 1;
    }

    /**
     * Check if client accepts gzip encoding
     */
    private function acceptsGzip(Request $request): bool
    {
        return strpos($request->header('Accept-Encoding'), 'gzip') !== false;
    }
}
