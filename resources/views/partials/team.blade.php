<!-- Guru kami Section -->
<section id="team" class="bg-gray-100 py-16">
  <!-- Section Title -->
  <div class="container mx-auto text-center mb-12" data-aos="fade-up">
    <h2 class="text-3xl font-bold">Guru Kami</h2>
  </div>

  <div class="container mx-auto px-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 justify-center">

        @if($teachers && count($teachers) > 0)
            @foreach ($teachers as $teacher)
                <div data-aos="fade-up" data-aos-delay="100" class="flex justify-center">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden max-w-xs w-full">
                        <!-- Photo Container with Fixed Aspect Ratio -->
                        <div class="relative w-full aspect-square overflow-hidden bg-gradient-to-br from-blue-50 to-gray-50">
                            @if($teacher->photo)
                                <img src="{{ asset('storage/' . $teacher->photo) }}" 
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" 
                                        alt="{{ $teacher->name ?? 'Teacher' }}"
                                        loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-gray-100">
                                    <div class="text-center">
                                        <i class="fas fa-user-tie text-gray-400 text-4xl mb-2"></i>
                                        <p class="text-xs text-gray-500">Foto Guru</p>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Hover Overlay with Social Links -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300">
                                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
                                    <a href="#" class="bg-white/20 backdrop-blur-sm rounded-full p-2 hover:bg-blue-500/80 transition-colors duration-200" title="Email">
                                        <i class="fas fa-envelope text-white text-sm"></i>
                                    </a>
                                    <a href="#" class="bg-white/20 backdrop-blur-sm rounded-full p-2 hover:bg-blue-500/80 transition-colors duration-200" title="WhatsApp">
                                        <i class="fab fa-whatsapp text-white text-sm"></i>
                                    </a>
                                    <a href="#" class="bg-white/20 backdrop-blur-sm rounded-full p-2 hover:bg-blue-500/80 transition-colors duration-200" title="LinkedIn">
                                        <i class="fab fa-linkedin text-white text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Teacher Info -->
                        <div class="p-6 text-center">
                            <h4 class="text-lg font-bold text-gray-800 mb-2 leading-tight">{{ $teacher->name ?? 'Unknown Teacher' }}</h4>
                            <span class="text-sm text-blue-600 font-medium bg-blue-50 px-3 py-1 rounded-full">{{ $teacher->title ?? 'Teacher' }}</span>
                            
                            @if($teacher->description)
                                <div class="mt-3 text-xs text-gray-600 line-clamp-2">
                                    {!! Str::limit(strip_tags($teacher->description), 80) !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-span-full text-center py-8">
                <p class="text-gray-600">Belum ada data guru yang tersedia.</p>
            </div>
        @endif

    </div>
  </div>
</section>
