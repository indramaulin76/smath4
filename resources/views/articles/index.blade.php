@extends('layouts.site')

@section('content')
<div class="bg-gray-100 py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900">Semua Berita</h1>
            <p class="text-gray-600 mt-2">Informasi terkini dari SMA Tunas Harapan</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($articles as $article)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="relative overflow-hidden aspect-video">
                        @if($article->image)
                            <img src="{{ asset('storage/' . $article->image) }}" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" 
                                 alt="{{ $article->title ?? 'Article Image' }}">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4">
                            <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $article->published_at ? $article->published_at->format('d M Y') : ($article->created_at ? $article->created_at->format('d M Y') : 'No Date') }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">{{ $article->title ?? 'Untitled Article' }}</h3>
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
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-newspaper text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Berita</h3>
                    <p class="text-gray-500">Berita akan segera ditampilkan di sini.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
            <div class="mt-12">
                {{ $articles->links() }}
            </div>
        @endif

        <!-- Back to Home -->
        <div class="text-center mt-12">
            <a href="{{ url('/#berita') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
