<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $maxRequests = 60, int $decayMinutes = 1): Response
    {
        $key = 'rate_limit:' . $request->ip();
        
        try {
            $current = Redis::incr($key);
            
            if ($current === 1) {
                Redis::expire($key, $decayMinutes * 60);
            }
            
            if ($current > $maxRequests) {
                return response()->json([
                    'ok' => false,
                    'error' => [
                        'code' => 'RATE_LIMIT_EXCEEDED',
                        'message' => 'Too many requests. Please try again later.',
                        'trace_id' => uniqid('rl_', true)
                    ]
                ], 429);
            }
        } catch (\Exception $e) {
            // Если Redis недоступен, пропускаем запрос
            // В продакшене лучше логировать это
        }
        
        $response = $next($request);
        
        $remaining = max(0, $maxRequests - ($current ?? 0));
        $response->headers->set('X-RateLimit-Limit', $maxRequests);
        $response->headers->set('X-RateLimit-Remaining', $remaining);
        
        return $response;
    }
}

