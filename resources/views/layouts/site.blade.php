<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SMA TUNAS HARAPAN</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo-sma-tunas-harapan.png') }}">    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS untuk Header -->
    <style>
        .header-title {
            font-size: 20px !important;
            font-weight: bold !important;
            color: white !important;
            margin: 0 !important;
            line-height: 1.2 !important;
            display: block !important;
            visibility: visible !important;
        }
        .header-subtitle {
            font-size: 12px !important;
            color: #bfdbfe !important;
            margin: 0 !important;
            display: block !important;
            visibility: visible !important;
        }
        .header-container {
            display: flex !important;
            align-items: center !important;
            gap: 16px !important;
        }
        .text-container {
            display: flex !important;
            flex-direction: column !important;
        }
        
        /* Media Query for Navigation */
        @media (min-width: 768px) {
            .desktop-nav {
                display: flex !important;
                gap: 1rem;
            }
            .mobile-menu-btn {
                display: none !important;
            }
        }
        
        @media (max-width: 767px) {
            .desktop-nav {
                display: none !important;
            }
            .mobile-menu-btn {
                display: block !important;
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <!-- Mobile Navigation Styles -->
    <style>
        /* Desktop navigation - hidden on mobile */
        .desktop-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Mobile menu button - hidden on desktop */
        .mobile-menu-btn {
            display: none;
        }
        
        /* Mobile styles */
        @media (max-width: 768px) {
            .desktop-nav {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
            }
        }
        
        /* Mobile menu transition */
        [x-show] {
            transition: all 0.3s ease;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <!-- Overlay untuk menutup menu mobile saat klik di area lain -->
    <div x-data="{ open: false }" @click.away="open = false" class="relative">
        <header style="background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%); padding: 1rem 0; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem;">
                <a href="{{ url('/') }}" style="display: flex; align-items: center; text-decoration: none; gap: 1rem;">
                    <img src="{{ asset('images/logo-sma-tunas-harapan.png') }}" 
                         alt="SMA Tunas Harapan Logo" 
                         style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                    <div style="display: flex; flex-direction: column;">
                        <span style="color: white; font-size: 22px; font-weight: 700; margin: 0; line-height: 1.2; font-family: 'Arial', sans-serif;">SMA TUNAS HARAPAN</span>
                        <span style="color: #bfdbfe; font-size: 13px; margin: 0; font-family: 'Arial', sans-serif;">Membentuk Generasi Unggul & Berakhlak</span>
                    </div>
                </a>

                <nav class="desktop-nav">
                    <a href="{{ url('/') }}#hero" style="color: white; text-decoration: none; padding: 0.5rem 1rem; font-weight: 500; transition: color 0.3s;">Beranda</a>
                    <a href="{{ url('/') }}#about" style="color: white; text-decoration: none; padding: 0.5rem 1rem; font-weight: 500; transition: color 0.3s;">Tentang kami</a>
                    <a href="{{ url('/') }}#services" style="color: white; text-decoration: none; padding: 0.5rem 1rem; font-weight: 500; transition: color 0.3s;">Program unggulan</a>
                    <a href="{{ url('/') }}#team" style="color: white; text-decoration: none; padding: 0.5rem 1rem; font-weight: 500; transition: color 0.3s;">Guru kami</a>
                    <a href="{{ url('/') }}#berita" style="color: white; text-decoration: none; padding: 0.5rem 1rem; font-weight: 500; transition: color 0.3s;">Berita</a>
                    <a href="{{ url('/') }}#contact" style="color: white; text-decoration: none; padding: 0.5rem 1rem; font-weight: 500; transition: color 0.3s;">Contact</a>
                </nav>

                <div @click="open = !open" class="mobile-menu-btn">
                    <button style="color: white; background: none; border: none; padding: 0.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; border-radius: 4px; transition: background-color 0.3s;" 
                            @mouseenter="$el.style.backgroundColor = 'rgba(255,255,255,0.1)'" 
                            @mouseleave="$el.style.backgroundColor = 'transparent'">
                        <svg x-show="!open" style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                        <svg x-show="open" style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 style="background-color: #1e40af; border-top: 1px solid rgba(255,255,255,0.1); position: absolute; width: 100%; top: 100%; left: 0;">
                <nav style="display: flex; flex-direction: column; padding: 1rem 1.5rem; gap: 0.25rem;">
                    <a href="{{ url('/') }}#hero" 
                       @click="open = false"
                       style="color: white; text-decoration: none; padding: 0.75rem 0; font-weight: 500; border-bottom: 1px solid rgba(255,255,255,0.1); transition: background-color 0.3s;"
                       @mouseenter="$el.style.backgroundColor = 'rgba(255,255,255,0.1)'" 
                       @mouseleave="$el.style.backgroundColor = 'transparent'">Beranda</a>
                    <a href="{{ url('/') }}#about" 
                       @click="open = false"
                       style="color: white; text-decoration: none; padding: 0.75rem 0; font-weight: 500; border-bottom: 1px solid rgba(255,255,255,0.1); transition: background-color 0.3s;"
                       @mouseenter="$el.style.backgroundColor = 'rgba(255,255,255,0.1)'" 
                       @mouseleave="$el.style.backgroundColor = 'transparent'">Tentang kami</a>
                    <a href="{{ url('/') }}#services" 
                       @click="open = false"
                       style="color: white; text-decoration: none; padding: 0.75rem 0; font-weight: 500; border-bottom: 1px solid rgba(255,255,255,0.1); transition: background-color 0.3s;"
                       @mouseenter="$el.style.backgroundColor = 'rgba(255,255,255,0.1)'" 
                       @mouseleave="$el.style.backgroundColor = 'transparent'">Program unggulan</a>
                    <a href="{{ url('/') }}#team" 
                       @click="open = false"
                       style="color: white; text-decoration: none; padding: 0.75rem 0; font-weight: 500; border-bottom: 1px solid rgba(255,255,255,0.1); transition: background-color 0.3s;"
                       @mouseenter="$el.style.backgroundColor = 'rgba(255,255,255,0.1)'" 
                       @mouseleave="$el.style.backgroundColor = 'transparent'">Guru kami</a>
                    <a href="{{ url('/') }}#berita" 
                       @click="open = false"
                       style="color: white; text-decoration: none; padding: 0.75rem 0; font-weight: 500; border-bottom: 1px solid rgba(255,255,255,0.1); transition: background-color 0.3s;"
                       @mouseenter="$el.style.backgroundColor = 'rgba(255,255,255,0.1)'" 
                       @mouseleave="$el.style.backgroundColor = 'transparent'">Berita</a>
                    <a href="{{ url('/') }}#contact" 
                       @click="open = false"
                       style="color: white; text-decoration: none; padding: 0.75rem 0; font-weight: 500; transition: background-color 0.3s;"
                       @mouseenter="$el.style.backgroundColor = 'rgba(255,255,255,0.1)'" 
                       @mouseleave="$el.style.backgroundColor = 'transparent'">Contact</a>
                </nav>
            </div>
        </header>

        <main>
            @yield('content')
        </main>
    </div>

    <footer class="bg-gray-100">
        <div class="container mx-auto py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="{{ asset('images/logo-sma-tunas-harapan.png') }}" alt="SMA Tunas Harapan" class="h-12 mr-3">
                        <h3 class="text-lg font-bold">SMA TUNAS HARAPAN</h3>
                    </div>
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
