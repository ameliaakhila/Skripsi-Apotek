<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressResponse
{
    /**
     * Handle an incoming request - Enable gzip compression for responses
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if client accepts gzip encoding
        if (!$this->acceptsGzip($request)) {
            return $response;
        }

        // Get response content
        $content = $response->getContent();

        // Only compress if content is large enough (> 1KB)
        if (strlen($content) > 1024) {
            // Compress the content
            $compressed = gzencode($content, 6); // 6 is optimal compression level

            if ($compressed !== false && strlen($compressed) < strlen($content)) {
                // Set compressed content
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Vary', 'Accept-Encoding');
                $response->headers->set('Content-Length', strlen($compressed));
            }
        }

        return $response;
    }

    /**
     * Check if client accepts gzip encoding
     */
    private function acceptsGzip(Request $request): bool
    {
        $acceptEncoding = $request->header('Accept-Encoding', '');
        return strpos($acceptEncoding, 'gzip') !== false;
    }
}
