<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply security headers in production
        if (app()->environment('production') || config('production.security.security_headers', false)) {
            // Prevent clickjacking
            $response->headers->set('X-Frame-Options', 'DENY');
            
            // Prevent MIME type sniffing
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            
            // Enable XSS protection
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            
            // Referrer policy
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            // HSTS (HTTP Strict Transport Security) - only if HTTPS
            if ($request->isSecure()) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            }
            
            // Content Security Policy
            if (config('production.security.content_security_policy', false)) {
                $csp = "default-src 'self'; " .
                       "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                       "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                       "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; " .
                       "img-src 'self' data: https: blob:; " .
                       "connect-src 'self'; " .
                       "media-src 'self'; " .
                       "object-src 'none'; " .
                       "base-uri 'self'; " .
                       "form-action 'self'; " .
                       "frame-ancestors 'none';";
                       
                $response->headers->set('Content-Security-Policy', $csp);
            }
            
            // Remove server information
            $response->headers->remove('Server');
            $response->headers->remove('X-Powered-By');
        }

        return $response;
    }
}
