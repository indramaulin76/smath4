<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 Verifikasi Dua Faktor - SMA Tunas Harapan</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
        
        .verification-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
            margin: 1rem;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: block;
        }
        
        .input-group {
            margin-bottom: 1rem;
        }
        
        .input-field {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            text-align: center;
            letter-spacing: 0.2em;
            font-weight: 600;
        }
        
        .input-field:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn-verify {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }
        
        .alert {
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }
    </style>
</head>
<body>
    <div class="verification-card">
        <img src="{{ asset('images/logo-sma-tunas-harapan.png') }}" alt="SMA Tunas Harapan" class="logo">
        
        <h2 style="text-align: center; margin-bottom: 0.5rem; color: #1f2937; font-size: 1.5rem;">🔐 Verifikasi Keamanan</h2>
        <p style="text-align: center; color: #6b7280; margin-bottom: 2rem; font-size: 0.9rem;">
            Masukkan kode 6 digit yang telah dikirim ke email Anda
        </p>
        
        @if(session('message'))
            <div class="alert alert-info">
                {{ session('message') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('admin.verify-2fa.submit') }}">
            @csrf
            
            <div class="input-group">
                <label for="code" style="display: block; margin-bottom: 0.5rem; color: #374151; font-weight: 600;">Kode Verifikasi</label>
                <input type="text" 
                       id="code" 
                       name="code" 
                       class="input-field" 
                       placeholder="000000"
                       maxlength="6"
                       pattern="[0-9]{6}"
                       required
                       autocomplete="off">
            </div>
            
            <button type="submit" class="btn-verify">
                ✅ Verifikasi Kode
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 1.5rem;">
            <p style="color: #6b7280; font-size: 0.8rem; margin-bottom: 0.5rem;">
                ⏱️ Kode berlaku selama 5 menit
            </p>
            <form method="POST" action="{{ route('admin.resend-2fa') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #3b82f6; cursor: pointer; text-decoration: underline; font-size: 0.9rem;">
                    📧 Kirim Ulang Kode
                </button>
            </form>
        </div>
        
        <div style="text-align: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('filament.admin.auth.logout') }}" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">
                🚪 Logout
            </a>
        </div>
    </div>

    <script>
        // Auto-focus on code input
        document.getElementById('code').focus();
        
        // Only allow numbers
        document.getElementById('code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Auto-submit when 6 digits entered
        document.getElementById('code').addEventListener('input', function(e) {
            if (this.value.length === 6) {
                setTimeout(() => {
                    this.closest('form').submit();
                }, 500);
            }
        });
    </script>
</body>
</html>
