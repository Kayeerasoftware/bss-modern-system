<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $key = 'api'): Response
    {
        $identifier = $request->ip();
        
        // Different limits for different endpoints
        $limits = [
            'locations' => [60, 1], // 60 requests per minute
            'members' => [30, 1],   // 30 requests per minute
            'search' => [20, 1],    // 20 requests per minute
        ];

        $limit = $limits[$key] ?? [100, 1]; // Default: 100 requests per minute

        if (RateLimiter::tooManyAttempts($identifier . ':' . $key, $limit[0])) {
            return response()->json([
                'error' => 'Too many requests. Please try again later.',
                'retry_after' => RateLimiter::availableIn($identifier . ':' . $key)
            ], 429);
        }

        RateLimiter::hit($identifier . ':' . $key, $limit[1] * 60);

        return $next($request);
    }
}