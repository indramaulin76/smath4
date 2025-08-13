<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminIPWhitelist
{
    /**
     * Daftar IP yang diizinkan mengakses admin panel
     * Tambahkan IP kantor/rumah admin di sini
     */
    private $allowedIPs = [
        '127.0.0.1',        // Localhost
        '::1',              // IPv6 localhost
        '192.168.1.0/24',   // Local network range
        // '203.123.45.67',    // IP kantor (contoh)
        // '180.234.56.78',    // IP rumah admin (contoh)
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // IP Whitelist DISABLED - Too restrictive for normal use
        // Enable this only if you have specific security requirements
        // and can maintain a list of static IP addresses
        
        /*
        $userIP = $request->ip();
        
        // Skip validation in development
        if (app()->environment('local')) {
            return $next($request);
        }
        
        // Check if IP is allowed
        if (!$this->isIPAllowed($userIP)) {
            // Log unauthorized access attempt
            \Log::warning('Unauthorized admin access attempt', [
                'ip' => $userIP,
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'timestamp' => now()
            ]);
            
            // Return 404 to hide admin panel existence
            abort(404, 'Page not found');
        }
        */
        
        // Currently allowing all IPs - other security layers will protect
        return $next($request);
    }

    /**
     * Check if IP is in allowed list
     */
    private function isIPAllowed($ip): bool
    {
        foreach ($this->allowedIPs as $allowedIP) {
            if ($this->ipInRange($ip, $allowedIP)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if IP is in range (supports CIDR notation)
     */
    private function ipInRange($ip, $range): bool
    {
        if (strpos($range, '/') === false) {
            // Single IP
            return $ip === $range;
        }
        
        // CIDR range
        list($subnet, $mask) = explode('/', $range);
        return (ip2long($ip) & ~((1 << (32 - $mask)) - 1)) === ip2long($subnet);
    }
}
