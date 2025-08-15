{{-- About Section --}}
<section id="about" class="py-20 bg-gradient-to-r from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-800 mb-6">Tentang Sekolah</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mb-8"></div>
            <p class="text-lg lg:text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
                {{ ($profile && $profile->about) ? $profile->about : 'Informasi tentang sekolah tidak tersedia.' }}
            </p>
        </div>

        {{-- Featured Image --}}
        @if($profile && $profile->featured_image)
        <div class="mb-20">
            <div class="rounded-2xl shadow-xl overflow-hidden">
                <div class="relative">
                    <img src="/storage/{{ $profile->featured_image }}" 
                         alt="Gambar Utama Sekolah" 
                         class="w-full h-[300px] sm:h-[400px] md:h-[500px] lg:h-[600px] object-cover"
                         onerror="console.log('Featured image failed to load:', this.src); this.style.backgroundColor='#f3f4f6'; this.alt='Gambar tidak dapat dimuat';"
                         onload="console.log('Featured image loaded successfully:', this.src)">
                </div>
            </div>
        </div>
        @endif

        {{-- Vision & Mission Grid --}}
        <div class="grid md:grid-cols-2 gap-8 lg:gap-12 mb-20">
            {{-- Vision --}}
            <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-10 transform hover:scale-105 transition-all duration-300">
                <div class="flex items-center mb-8">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mr-6">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800">Visi</h3>
                </div>
                <div class="text-gray-700 leading-relaxed text-lg">
                    {!! ($profile && $profile->vision) ? nl2br(e($profile->vision)) : 'Visi sekolah belum diisi.' !!}
                </div>
            </div>

            {{-- Mission --}}
            <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-10 transform hover:scale-105 transition-all duration-300">
                <div class="flex items-center mb-8">
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mr-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800">Misi</h3>
                </div>
                <div class="text-gray-700 leading-relaxed text-lg">
                    {{ ($profile && $profile->mission) ? $profile->mission : 'Misi sekolah belum diisi.' }}
                </div>
            </div>
        </div>

        {{-- School Gallery --}}
        @if($profile && $profile->gallery && is_array($profile->gallery) && count($profile->gallery) > 0)
        <div class="mb-20">
            <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-12">
                {{-- Gallery Header --}}
                <div class="text-center mb-12">
                    <h3 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-6">Galeri Sekolah</h3>
                    <div class="w-20 h-1 bg-gradient-to-r from-blue-600 to-indigo-600 mx-auto mb-6"></div>
                    <p class="text-gray-600 text-lg">Koleksi foto-foto kegiatan dan fasilitas sekolah kami</p>
                </div>

                {{-- Gallery Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    @foreach($profile->gallery as $index => $image)
                    <div class="group relative rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                        <div class="aspect-w-16 aspect-h-12 relative">
                            <img src="/storage/{{ $image }}" 
                                 alt="Galeri Sekolah {{ $index + 1 }}" 
                                 class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300"
                                 onerror="console.log('Gallery image failed:', this.src);"
                                 onload="console.log('Gallery image loaded:', this.src)">
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Gallery Footer --}}
                <div class="text-center mt-12">
                    <p class="text-gray-500 text-sm">Lihat lebih banyak aktivitas sekolah kami</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>