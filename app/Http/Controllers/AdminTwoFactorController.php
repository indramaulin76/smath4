<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminTwoFactorController extends Controller
{
    /**
     * Show 2FA verification form
     */
    public function show()
    {
        return view('admin.verify-2fa');
    }
    
    /**
     * Verify 2FA code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6'
        ]);
        
        $inputCode = $request->input('code');
        $storedCode = Session::get('admin_2fa_code');
        $expires = Session::get('admin_2fa_expires');
        
        // Check if code exists and not expired
        if (!$storedCode || !$expires || now()->isAfter($expires)) {
            return back()->withErrors(['code' => 'Kode verifikasi telah kedaluwarsa. Silakan minta kode baru.']);
        }
        
        // Verify code
        if ($inputCode !== $storedCode) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid.']);
        }
        
        // Mark 2FA as verified for this session
        Session::put('admin_2fa_verified', true);
        Session::forget(['admin_2fa_code', 'admin_2fa_expires']);
        
        // Log successful verification
        \Log::info('2FA verification successful', [
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'timestamp' => now()
        ]);
        
        return redirect()->intended('/smath-admin-secure-2025');
    }
    
    /**
     * Resend 2FA code
     */
    public function resend()
    {
        $user = auth()->user();
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        Session::put('admin_2fa_code', $code);
        Session::put('admin_2fa_expires', now()->addMinutes(5));
        
        try {
            \Mail::raw(
                "Kode verifikasi admin SMA Tunas Harapan: {$code}\n\nKode berlaku selama 5 menit.\n\nJika Anda tidak melakukan login, abaikan email ini.",
                function ($message) use ($user) {
                    $message->to($user->email)
                           ->subject('🔐 Kode Verifikasi Admin (Kirim Ulang) - SMA Tunas Harapan');
                }
            );
            
            return back()->with('message', 'Kode verifikasi baru telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            \Log::error('Failed to resend 2FA code', ['error' => $e->getMessage()]);
            return back()->withErrors(['code' => 'Gagal mengirim kode. Silakan coba lagi.']);
        }
    }
}
