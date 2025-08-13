<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚠️ Akses Dibatasi - SMA Tunas Harapan</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
        
        .error-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
            margin: 1rem;
            text-align: center;
        }
        
        .error-icon {
            font-size: 4rem;
            color: #dc2626;
            margin-bottom: 1rem;
        }
        
        .retry-timer {
            background: #fee2e2;
            color: #dc2626;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .info-box {
            background: #f3f4f6;
            border-left: 4px solid #6b7280;
            padding: 1rem;
            margin: 1rem 0;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">🚫</div>
        
        <h1 style="color: #1f2937; margin-bottom: 1rem; font-size: 1.8rem;">Akses Dibatasi</h1>
        
        <p style="color: #6b7280; margin-bottom: 1.5rem; font-size: 1.1rem;">
            {{ $message ?? 'Terlalu banyak percobaan akses dalam waktu singkat.' }}
        </p>
        
        <div class="retry-timer">
            ⏳ Silakan coba lagi dalam {{ $retry_after ?? 60 }} detik
        </div>
        
        <div class="info-box">
            <h3 style="color: #374151; margin-bottom: 0.5rem; font-size: 1rem;">🔒 Mengapa ini terjadi?</h3>
            <ul style="color: #6b7280; font-size: 0.9rem; margin: 0; padding-left: 1rem;">
                <li>Sistem keamanan mendeteksi aktivitas yang mencurigakan</li>
                <li>Terlalu banyak percobaan akses dalam waktu singkat</li>
                <li>Ini adalah perlindungan terhadap serangan brute force</li>
            </ul>
        </div>
        
        <div class="info-box">
            <h3 style="color: #374151; margin-bottom: 0.5rem; font-size: 1rem;">✅ Apa yang harus dilakukan?</h3>
            <ul style="color: #6b7280; font-size: 0.9rem; margin: 0; padding-left: 1rem;">
                <li>Tunggu hingga timer selesai</li>
                <li>Pastikan Anda menggunakan kredensial yang benar</li>
                <li>Hubungi administrator jika masalah berlanjut</li>
            </ul>
        </div>
        
        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
            <a href="{{ url('/') }}" style="color: #3b82f6; text-decoration: none; font-weight: 500;">
                🏠 Kembali ke Halaman Utama
            </a>
        </div>
    </div>

    <script>
        // Auto refresh when timer expires
        const retryAfter = {{ $retry_after ?? 60 }};
        setTimeout(() => {
            window.location.reload();
        }, retryAfter * 1000);
        
        // Countdown timer
        let timeLeft = retryAfter;
        const timer = document.querySelector('.retry-timer');
        
        const countdown = setInterval(() => {
            timeLeft--;
            if (timeLeft <= 0) {
                clearInterval(countdown);
                timer.innerHTML = '✅ Silakan refresh halaman ini';
            } else {
                timer.innerHTML = `⏳ Silakan coba lagi dalam ${timeLeft} detik`;
            }
        }, 1000);
    </script>
</body>
</html>
