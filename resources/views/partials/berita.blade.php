<!-- /Berita Section -->
<section id="berita" class="py-16">
  <div class="container mx-auto px-4">
    <div class="text-center mb-12" data-aos="fade-up">
      <h2 class="text-3xl font-bold">Berita Terbaru</h2>
      <p class="text-gray-600 mt-2">Informasi terkini dari SMA Tunas Harapan</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach ($articles->where('is_published', true)->take(6) as $article)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="relative overflow-hidden aspect-video">
                    <img src="{{ $article->image_url }}"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" 
                         alt="{{ $article->title }}">
                    <div class="absolute top-4 left-4">
                        <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                            {{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <h5 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">{{ $article->title }}</h5>
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        {{ $article->excerpt ?: Str::limit(strip_tags($article->content), 120) }}
                    </p>
                    <div class="flex items-center justify-between">
                        <a href="{{ route('articles.show', $article) }}" 
                           class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium transition-colors duration-200">
                            Baca Selengkapnya
                            <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                        <span class="text-sm text-gray-400">
                            <i class="far fa-clock mr-1"></i>
                            {{ $article->published_at ? $article->published_at->diffForHumans() : $article->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($articles->where('is_published', true)->count() > 6)
        <div class="text-center mt-12">
            <a href="{{ route('articles.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                Lihat Semua Berita
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    @endif
  </div>
</section><!-- /Berita Section -->
