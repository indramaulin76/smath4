<!-- About Section -->
<section id="about" class="bg-gray-100 py-16">

  <!-- Section Title -->
  <div class="container mx-auto text-center mb-12" data-aos="fade-up">
    <h2 class="text-3xl font-bold">
      {{ $profile->title ?: 'Tentang Kami' }}
    </h2>
    @if($profile->description)
      <p class="text-gray-600 mt-2">{{ $profile->description }}</p>
    @endif
  </div><!-- End Section Title -->

  <div class="container mx-auto px-4">

    <div class="flex flex-wrap -mx-3 mb-12">
      <div class="w-full lg:w-1/2 px-3" data-aos="fade-up" data-aos-delay="100">
        @if($profile->featured_image)
          <img src="{{ asset('storage/' . $profile->featured_image) }}" alt="{{ $profile->title }}" class="w-full rounded-lg shadow-lg">
        @else
          <img src="{{ asset('assets/img/aboutme1.jpeg') }}" alt="About Us" class="w-full rounded-lg shadow-lg">
        @endif
      </div>

      <div class="w-full lg:w-1/2 px-3 flex flex-col justify-center" data-aos="fade-up" data-aos-delay="200">
        <div class="pl-0 lg:pl-6">
          <div class="flex items-center mb-4">
            <h3 class="text-3xl font-bold text-gray-900">
              {{ $profile->title ?: 'SMA TUNAS HARAPAN' }}
            </h3>
            @if($profile->established_year)
              <span class="ml-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                Est. {{ $profile->established_year }}
              </span>
            @endif
          </div>
          
          @if($profile->principal_name)
            <p class="text-gray-600 mb-4">
              <strong>Kepala Sekolah:</strong> {{ $profile->principal_name }}
            </p>
          @endif

          <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-700 mb-6">
            {!! $profile->about !!}
          </div>

          @if($profile->vision)
            <div class="bg-white p-6 rounded-lg shadow-md mb-4">
              <h4 class="text-xl font-bold text-blue-600 mb-3 flex items-center">
                <i class="fas fa-eye mr-2"></i>
                VISI
              </h4>
              <div class="prose max-w-none prose-p:text-gray-700">
                {!! $profile->vision !!}
              </div>
            </div>
          @endif

          @if($profile->mission)
            <div class="bg-white p-6 rounded-lg shadow-md">
              <h4 class="text-xl font-bold text-green-600 mb-3 flex items-center">
                <i class="fas fa-bullseye mr-2"></i>
                MISI
              </h4>
              <div class="prose max-w-none prose-p:text-gray-700">
                {!! $profile->mission !!}
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Content Sections -->
    @if($profile->sections && count($profile->sections) > 0)
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        @foreach($profile->sections as $section)
          <div class="bg-white p-8 rounded-xl shadow-lg" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
            <h4 class="text-2xl font-bold text-gray-900 mb-4">{{ $section['title'] }}</h4>
            <div class="prose max-w-none prose-p:text-gray-700">
              {!! $section['content'] !!}
            </div>
          </div>
        @endforeach
      </div>
    @endif

    <!-- School Gallery -->
    @if($profile->gallery && count($profile->gallery) > 0)
      <div class="mb-12" data-aos="fade-up">
        <h3 class="text-3xl font-bold text-center mb-8">Galeri Sekolah</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          @foreach($profile->gallery as $image)
            <div class="group relative overflow-hidden rounded-lg aspect-square">
              <img src="{{ asset('storage/' . $image) }}" 
                   alt="Gallery Image" 
                   class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300 cursor-pointer"
                   onclick="openProfileModal('{{ asset('storage/' . $image) }}')">
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    <!-- Achievements & Facilities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Achievements -->
      @if($profile->achievements && count($profile->achievements) > 0)
        <div class="bg-white p-8 rounded-xl shadow-lg" data-aos="fade-up" data-aos-delay="100">
          <h4 class="text-2xl font-bold text-yellow-600 mb-6 flex items-center">
            <i class="fas fa-trophy mr-3"></i>
            Prestasi Sekolah
          </h4>
          <div class="space-y-4">
            @foreach($profile->achievements as $achievement)
              <div class="border-l-4 border-yellow-500 pl-4 py-2">
                <div class="flex items-center justify-between mb-1">
                  <h5 class="font-semibold text-gray-900">{{ $achievement['title'] }}</h5>
                  @if(isset($achievement['year']))
                    <span class="text-sm text-gray-500 font-medium">{{ $achievement['year'] }}</span>
                  @endif
                </div>
                @if(isset($achievement['description']))
                  <p class="text-gray-600 text-sm">{{ $achievement['description'] }}</p>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif

      <!-- Facilities -->
      @if($profile->facilities && count($profile->facilities) > 0)
        <div class="bg-white p-8 rounded-xl shadow-lg" data-aos="fade-up" data-aos-delay="200">
          <h4 class="text-2xl font-bold text-purple-600 mb-6 flex items-center">
            <i class="fas fa-building mr-3"></i>
            Fasilitas Sekolah
          </h4>
          <div class="space-y-4">
            @foreach($profile->facilities as $facility)
              <div class="flex items-start">
                <i class="fas fa-check-circle text-purple-500 mt-1 mr-3"></i>
                <div>
                  <h5 class="font-semibold text-gray-900">{{ $facility['name'] }}</h5>
                  @if(isset($facility['description']))
                    <p class="text-gray-600 text-sm mt-1">{{ $facility['description'] }}</p>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>

  </div>

</section><!-- /About Section -->

<!-- Modal for Profile Gallery Images -->
<div id="profileImageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <img id="profileModalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
        <button onclick="closeProfileModal()" 
                class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75 transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>
function openProfileModal(imageSrc) {
    document.getElementById('profileModalImage').src = imageSrc;
    document.getElementById('profileImageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeProfileModal() {
    document.getElementById('profileImageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside the image
document.getElementById('profileImageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeProfileModal();
    }
});
</script>
