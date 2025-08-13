<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminTwoFactor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 2FA DISABLED - Enable this when email configuration is ready
        // This requires proper SMTP configuration to work
        
        /*
        // Skip 2FA check for login page
        if ($request->routeIs('filament.admin.auth.login')) {
            return $next($request);
        }
        
        // Check if user is authenticated
        if (!auth()->check()) {
            return $next($request);
        }
        
        // Check if 2FA is verified for this session
        if (!Session::has('admin_2fa_verified')) {
            // Generate and send 2FA code
            $this->send2FACode();
            
            return redirect()->route('admin.verify-2fa')
                ->with('message', 'Kode verifikasi telah dikirim ke email Anda.');
        }
        */
        
        // Currently allowing access without 2FA - configure email first
        return $next($request);
    }
    
    /**
     * Send 2FA verification code
     */
    private function send2FACode(): void
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        Session::put('admin_2fa_code', $code);
        Session::put('admin_2fa_expires', now()->addMinutes(5));
        
        // Send email with code
        $user = auth()->user();
        
        try {
            \Mail::raw(
                "Kode verifikasi admin SMA Tunas Harapan: {$code}\n\nKode berlaku selama 5 menit.\n\nJika Anda tidak melakukan login, abaikan email ini.",
                function ($message) use ($user) {
                    $message->to($user->email)
                           ->subject('🔐 Kode Verifikasi Admin - SMA Tunas Harapan');
                }
            );
            
            \Log::info('2FA code sent', ['user_id' => $user->id, 'email' => $user->email]);
        } catch (\Exception $e) {
            \Log::error('Failed to send 2FA code', ['error' => $e->getMessage()]);
        }
    }
}
