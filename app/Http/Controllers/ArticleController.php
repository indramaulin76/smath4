<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::where('is_published', true)
            ->where(function($query) {
                $query->whereNotNull('published_at')
                      ->where('published_at', '<=', now())
                      ->orWhereNull('published_at');
            })
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('articles.index', compact('articles'));
    }

    public function show(Article $article)
    {
        // Pastikan artikel sudah dipublikasi
        if (!$article->is_published) {
            abort(404);
        }

        // Jika published_at null atau masa depan, gunakan created_at untuk validasi
        if ($article->published_at && $article->published_at->isFuture()) {
            abort(404);
        }

        // Ambil artikel terkait (3 artikel terbaru selain yang sedang dibaca)
        $relatedArticles = Article::where('is_published', true)
            ->where(function($query) {
                $query->whereNotNull('published_at')
                      ->where('published_at', '<=', now())
                      ->orWhereNull('published_at');
            })
            ->where('id', '!=', $article->id)
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }
}
