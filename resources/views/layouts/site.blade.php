<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SMA TUNAS HARAPAN</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
</head>

<body class="font-sans antialiased">
    <header x-data="{ open: false }" class="sticky-header">
        <div class="container mx-auto relative flex items-center justify-between py-3 px-4">
            <a href="{{ url('/') }}" class="flex items-center">
                <img src="{{ asset('assets/img/LOGO SMA TERBARU.png') }}" alt="" class="h-12 mr-3">
                <h1 class="text-xl font-bold">SMA TUNAS HARAPAN</h1>
            </a>

            <nav class="hidden md:flex space-x-8">
                <a href="{{ url('/') }}#hero" class="text-gray-600 hover:text-gray-900">Beranda</a>
                <a href="{{ url('/') }}#about" class="text-gray-600 hover:text-gray-900">Tentang kami</a>
                <a href="{{ url('/') }}#services" class="text-gray-600 hover:text-gray-900">Program unggulan</a>
                <a href="{{ url('/') }}#team" class="text-gray-600 hover:text-gray-900">Guru kami</a>
                <a href="{{ url('/') }}#berita" class="text-gray-600 hover:text-gray-900">Berita</a>
                <a href="{{ url('/') }}#contact" class="text-gray-600 hover:text-gray-900">Contact</a>
            </nav>

            <div @click="open = !open" class="md:hidden">
                <button class="text-gray-600 hover:text-gray-900">
                    <svg :class="{'hidden': open, 'block': !open }" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                    <svg :class="{'block': open, 'hidden': !open }" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div x-show="open" class="md:hidden">
            <nav class="flex flex-col px-4 pt-2 pb-4 space-y-2">
                <a href="{{ url('/') }}#hero" class="text-gray-600 hover:text-gray-900">Beranda</a>
                <a href="{{ url('/') }}#about" class="text-gray-600 hover:text-gray-900">Tentang kami</a>
                <a href="{{ url('/') }}#services" class="text-gray-600 hover:text-gray-900">Program unggulan</a>
                <a href="{{ url('/') }}#team" class="text-gray-600 hover:text-gray-900">Guru kami</a>
                <a href="{{ url('/') }}#berita" class="text-gray-600 hover:text-gray-900">Berita</a>
                <a href="{{ url('/') }}#contact" class="text-gray-600 hover:text-gray-900">Contact</a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-100">
        <div class="container mx-auto py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold">SMA TUNAS HARAPAN</h3>
                    <p class="mt-2 text-gray-600">Komplek BNI 46, pesing, Wijaya Kusuma, West Jakarta City</p>
                    <p class="mt-2 text-gray-600"><strong>Phone:</strong> (021) 5660066</p>
                    <p class="mt-2 text-gray-600"><strong>Email:</strong> Smatunasharapanadm@gmail.com</p>
                </div>
                <div>
                    <h4 class="text-lg font-bold">Useful Links</h4>
                    <ul class="mt-2 space-y-2">
                        <li><a href="{{ url('/') }}" class="text-gray-600 hover:text-gray-900">Beranda</a></li>
                        <li><a href="{{ url('/') }}#about" class="text-gray-600 hover:text-gray-900">Tentang kami</a></li>
                        <li><a href="{{ url('/') }}#services" class="text-gray-600 hover:text-gray-900">Program unggulan</a></li>
                        <li><a href="{{ url('/') }}#team" class="text-gray-600 hover:text-gray-900">Guru kami</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold">Follow Us</h4>
                    <p class="mt-2 text-gray-600">ikuti juga sosial media kami</p>
                    <div class="mt-2 flex space-x-4">
                        <a href="https://www.tiktok.com/@sma_toenhard" target="_blank" class="text-gray-600 hover:text-gray-900">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="https://www.instagram.com/smatunasharapan/?utm_source=ig_web_button_share_sheet" target="_blank" class="text-gray-600 hover:text-gray-900">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/6281617707087" target="_blank" class="text-gray-600 hover:text-gray-900">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://youtube.com/@smatunasharapan?si=RfVDD2jtu8vP_2a2" target="_blank" class="text-gray-600 hover:text-gray-900">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-200 py-4 text-center">
            <p>© <span id="year"></span> <strong class="px-1">SMA Tunas Harapan</strong>. All rights reserved.</p>
            <p>Developed by <a href="https://github.com/indramaulin76" target="_blank" class="text-blue-600 hover:underline">IndraMaulana</a></p>
        </div>
    </footer>

    <script>
        // Auto update year
        document.getElementById("year").textContent = new Date().getFullYear();
        
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
</body>

</html>
