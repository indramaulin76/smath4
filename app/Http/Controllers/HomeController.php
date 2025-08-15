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
        
        // Create default profile if none exists
        if (!$profile) {
            $profile = Profile::create([
                'school_name' => 'SMA Tunas Harapan',
                'about' => 'SMA swasta ini didirikan pertama kali pada tahun 1987 oleh Yayasan Danar Dana Swadharma. Sekarang SMA Tunas Harapan memakai panduan kurikulum belajar SMA 2013 IPS dan sedang menuju penerapan kurikulum merdeka.',
                'vision' => 'Menciptakan generasi muda yang berakhlakul mulia, unggul dalam IMTAQ, berkarakter dan santun dalam perilaku, berPrestasi dalam bidang akademik dan IPTEK',
                'mission' => 'Memberikan pendidikan berkualitas tinggi yang mengembangkan potensi akademik, karakter, dan spiritual siswa.',
                'gallery' => [],
                'achievements' => [],
                'facilities' => [],
                'sections' => []
            ]);
        }
        
        $services = Service::all();

        return view('welcome', compact('articles', 'teachers', 'profile', 'services'));
    }
}
