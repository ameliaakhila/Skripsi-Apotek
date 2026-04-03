<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Usage: Route middleware for checking if user has specific role(s)
     * Example: ->middleware('role:admin,manajer')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if user's role is in the allowed roles
        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Unauthorized access. Your role does not have permission to access this resource.');
        }

        return $next($request);
    }
}

