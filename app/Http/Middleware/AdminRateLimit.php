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
        
        // Skip rate limiting for file uploads and livewire requests
        if ($request->hasFile('image') || 
            $request->hasFile('background_image') ||
            $request->hasFile('photo') ||
            str_contains($request->path(), 'livewire') ||
            str_contains($request->path(), 'upload-file') ||
            $request->isMethod('POST') && $request->has('components')) {
            return $next($request);
        }
        
        // Allow 15 attempts per minute for admin access (increased for file operations)
        if (RateLimiter::tooManyAttempts($key, 15)) {
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
