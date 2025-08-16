@extends('layouts.site')

@section('content')
<div class="article-container bg-gray-100 pb-16">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <!-- Article Header -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 relative z-10">
                <div class="relative h-64 md:h-[400px] lg:h-[500px] overflow-hidden">
                <img src="{{ asset('storage/' . $article->image) }}" 
                    alt="Article image" 
                    class="w-full h-auto">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                </div>
                
                <div class="p-8">
                    <div class="mb-4">
                        <span class="text-sm text-gray-500">
                            {{ $article->published_at ? $article->published_at->format('d F Y') : $article->created_at->format('d F Y') }}
                        </span>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $article->title }}</h1>
                    
                    @if($article->excerpt)
                        <p class="text-xl text-gray-600 mb-6 leading-relaxed">{{ $article->excerpt }}</p>
                    @endif
                </div>
            </div>

            <!-- Article Content -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 relative z-10">
                <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-700 prose-a:text-blue-600 prose-strong:text-gray-900">
                    {!! $article->content !!}
                </div>
            </div>

            <!-- Gallery Section -->
            @if($article->gallery && count($article->gallery) > 0)
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8 relative z-10">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Galeri Foto</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($article->gallery as $image)
                            <div class="group relative overflow-hidden rounded-lg">
                                <div class="aspect-[4/3] overflow-hidden">
                             <img src="{{ asset('storage/' . $image) }}" 
                                 alt="Gallery image" 
                                 class="w-full h-auto"
                                 onclick="openModal('{{ asset('storage/' . $image) }}')">
                                </div>
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 rounded-lg"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Action Links -->
            @if($article->links && count($article->links) > 0)
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8 relative z-10">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Tautan Terkait</h3>
                    <div class="flex flex-wrap gap-4">
                        @foreach($article->links as $link)
                            <a href="{{ $link['url'] }}" 
                               target="_blank"
                               class="inline-flex items-center px-6 py-3 rounded-lg text-white font-medium transition-colors duration-200
                                      @switch($link['type'])
                                          @case('primary')
                                              bg-blue-600 hover:bg-blue-700
                                              @break
                                          @case('secondary')
                                              bg-gray-600 hover:bg-gray-700
                                              @break
                                          @case('download')
                                              bg-green-600 hover:bg-green-700
                                              @break
                                          @case('external')
                                              bg-purple-600 hover:bg-purple-700
                                              @break
                                          @default
                                              bg-blue-600 hover:bg-blue-700
                                      @endswitch">
                                @switch($link['type'])
                                    @case('download')
                                        <i class="fas fa-download mr-2"></i>
                                        @break
                                    @case('external')
                                        <i class="fas fa-external-link-alt mr-2"></i>
                                        @break
                                    @default
                                        <i class="fas fa-link mr-2"></i>
                                @endswitch
                                {{ $link['title'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Related Articles -->
            @if($relatedArticles && count($relatedArticles) > 0)
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8 relative z-10">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Artikel Terkait</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedArticles as $related)
                            <div class="group">
                                <a href="{{ route('articles.show', $related) }}" class="block">
                                    <div class="aspect-[16/10] overflow-hidden rounded-lg mb-3">
                                <img src="{{ asset('storage/' . $related->image) }}" 
                                    alt="Article image"
                                    class="w-full h-auto">
                                    </div>
                                    <h4 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors duration-200 line-clamp-2">
                                        {{ $related->title }}
                                    </h4>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $related->published_at ? $related->published_at->format('d F Y') : $related->created_at->format('d F Y') }}
                                    </p>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Navigation -->
            <div class="text-center relative z-10">
                <a href="{{ url('/#berita') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Daftar Berita
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Modal for Gallery Images -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-[9999] flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
    <img id="modalImage" src="" alt="Modal image" class="w-full h-auto max-w-full max-h-full object-contain rounded-lg">
        <button onclick="closeModal()" 
                class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75 transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>
function openModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside the image
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection
