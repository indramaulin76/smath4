<!-- Services Section -->
<section id="services" class="py-16">

  <!-- Section Title -->
  <div class="container mx-auto text-center mb-12" data-aos="fade-up">
    <h2 class="text-3xl font-bold">PROGRAM UNGULAN</h2>
  </div><!-- End Section Title -->

  <div class="container mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach ($services as $service)
            <div data-aos="fade-up" data-aos-delay="100">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    
                    <!-- Service Image -->
                    @if($service->image)
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $service->image_url }}"
                                 alt="{{ $service->title }}"
                                 class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                 loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                        </div>
                    @else
                        <!-- Default Image if no image uploaded -->
                        <div class="relative h-48 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <div class="text-white text-center">
                                <svg class="w-12 h-12 mx-auto mb-2 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-medium">{{ $service->title }}</span>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Service Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3 hover:text-blue-600 transition-colors">
                            {{ $service->title }}
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            {{ $service->description }}
                        </p>
                        
                        <!-- Learn More Button -->
                        <div class="mt-4">
                            <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium text-sm transition-colors">
                                Pelajari Lebih Lanjut
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div><!-- End Service Item -->
        @endforeach
    </div>
  </div>

</section><!-- /Services Section -->
