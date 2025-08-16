@php
    $heroSection = \App\Models\HeroSection::active()->ordered()->first();
@endphp

<!-- Hero Section -->
<section id="hero" class="relative min-h-[70vh] md:min-h-[80vh] flex items-center justify-center overflow-hidden" 
         style="background-color: {{ $heroSection->background_color ?? '#1f2937' }}; color: {{ $heroSection->text_color ?? '#ffffff' }};">
    
    <!-- Background Image (if exists) -->
    @if($heroSection && $heroSection->background_image)
        <div class="absolute inset-0 z-0">
          <img src="{{ asset('storage/' . $heroSection->background_image) }}" 
              alt="Background image" 
              class="w-full h-auto">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        </div>
    @else
        <!-- Gradient Background if no image -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700"></div>
    @endif
    
    <!-- Content Container -->
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            
            <!-- Main Title -->
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight" 
                data-aos="fade-up" data-aos-delay="100">
                @if($heroSection && $heroSection->title)
                    {!! $heroSection->title !!}
                @else
                    Welcome to <span class="text-blue-300">SMA Tunas Harapan</span>
                @endif
            </h1>
            
            <!-- Subtitle -->
            @if($heroSection && $heroSection->subtitle)
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-4 text-blue-200" 
                    data-aos="fade-up" data-aos-delay="200">
                    {{ $heroSection->subtitle }}
                </h2>
            @endif
            
            <!-- Description -->
            <p class="text-base sm:text-lg md:text-xl mb-8 md:mb-10 max-w-3xl mx-auto leading-relaxed opacity-90" 
               data-aos="fade-up" data-aos-delay="300">
                @if($heroSection && $heroSection->description)
                    {{ $heroSection->description }}
                @else
                    Bersama kita menciptakan generasi muda yang berakhlakul mulia, berprestasi, dan siap menghadapi masa depan.
                @endif
            </p>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center" 
                 data-aos="fade-up" data-aos-delay="400">
                
                @if($heroSection && $heroSection->primary_button_text)
                    <a href="{{ $heroSection->primary_button_url ?? '#' }}" 
                       class="inline-flex items-center px-6 py-3 md:px-8 md:py-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        {{ $heroSection->primary_button_text }}
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                @else
                    <a href="#about" 
                       class="inline-flex items-center px-6 py-3 md:px-8 md:py-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        Pelajari Lebih Lanjut
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                @endif
                
                @if($heroSection && $heroSection->secondary_button_text)
                    <a href="{{ $heroSection->secondary_button_url ?? '#' }}" 
                       class="inline-flex items-center px-6 py-3 md:px-8 md:py-4 border-2 border-white text-white hover:bg-white hover:text-gray-800 font-semibold rounded-lg transition-all duration-300 transform hover:scale-105">
                        {{ $heroSection->secondary_button_text }}
                        <i class="fas fa-external-link-alt ml-2"></i>
                    </a>
                @else
                    <a href="https://youtu.be/QY29k9_uCII?si=eQtnMSsiwiZbAAIt" 
                       class="glightbox inline-flex items-center px-6 py-3 md:px-8 md:py-4 border-2 border-white text-white hover:bg-white hover:text-gray-800 font-semibold rounded-lg transition-all duration-300 transform hover:scale-105">
                        <i class="far fa-play-circle mr-2 text-xl"></i>
                        Watch Video
                    </a>
                @endif
            </div>
            
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <a href="#about" class="text-white hover:text-blue-200 transition-colors">
            <i class="fas fa-chevron-down text-2xl"></i>
        </a>
    </div>
    
</section><!-- /Hero Section -->
