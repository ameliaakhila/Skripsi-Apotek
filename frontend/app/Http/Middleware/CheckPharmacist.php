<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPharmacist
{
    /**
     * Handle an incoming request.
     *
     * This middleware ensures that only users with 'apoteker' role can access the route.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== 'apoteker') {
            abort(403, 'Unauthorized. Only pharmacists can access this resource.');
        }

        return $next($request);
    }
}
