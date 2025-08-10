<!-- Guru kami Section -->
<section id="team" class="bg-gray-100 py-16">
  <!-- Section Title -->
  <div class="container mx-auto text-center mb-12" data-aos="fade-up">
    <h2 class="text-3xl font-bold">Guru Kami</h2>
  </div>

  <div class="container mx-auto px-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 justify-center">

        @foreach ($teachers as $teacher)
            <div data-aos="fade-up" data-aos-delay="100" class="flex justify-center">
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden max-w-xs w-full">
                    <div class="relative h-80 overflow-hidden bg-gray-50">
                        <img src="{{ asset('storage/' . $teacher->photo) }}" 
                                class="w-full h-full object-contain hover:scale-105 transition-transform duration-300" 
                                alt="{{ $teacher->name }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300">
                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-3">
                                <a href="#" class="bg-white/20 backdrop-blur-sm rounded-full p-2 hover:bg-white/30 transition-colors">
                                    <i class="fab fa-twitter text-white text-lg"></i>
                                </a>
                                <a href="#" class="bg-white/20 backdrop-blur-sm rounded-full p-2 hover:bg-white/30 transition-colors">
                                    <i class="fab fa-facebook text-white text-lg"></i>
                                </a>
                                <a href="#" class="bg-white/20 backdrop-blur-sm rounded-full p-2 hover:bg-white/30 transition-colors">
                                    <i class="fab fa-instagram text-white text-lg"></i>
                                </a>
                                <a href="#" class="bg-white/20 backdrop-blur-sm rounded-full p-2 hover:bg-white/30 transition-colors">
                                    <i class="fab fa-linkedin text-white text-lg"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 text-center">
                        <h4 class="text-lg font-bold text-gray-800 mb-1">{{ $teacher->name }}</h4>
                        <span class="text-sm text-gray-600 font-medium">{{ $teacher->title }}</span>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
  </div>
</section>
