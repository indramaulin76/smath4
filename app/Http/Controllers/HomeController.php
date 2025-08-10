<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;
use App\Models\Profile;
use App\Models\Service;
use App\Models\Teacher;

class HomeController extends Controller
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
            ->get();
            
        $teachers = Teacher::all();
        $profile = Profile::first();
        $services = Service::all();

        return view('welcome', compact('articles', 'teachers', 'profile', 'services'));
    }
}
