<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AdminRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'admin-access:' . $request->ip();
        
        // Allow 5 attempts per minute for admin access
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            // Log rate limit exceeded
            \Log::warning('Admin rate limit exceeded', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'available_in' => $seconds,
                'timestamp' => now()
            ]);
            
            return response()->view('errors.429', [
                'message' => 'Terlalu banyak percobaan akses. Silakan coba lagi dalam ' . $seconds . ' detik.',
                'retry_after' => $seconds
            ], 429);
        }
        
        RateLimiter::hit($key, 60); // 1 minute window
        
        return $next($request);
    }
}
