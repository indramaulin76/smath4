<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeroSection;

class HeroSectionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        HeroSection::create([
            'title' => 'Welcome to <span class="text-blue-300">SMA Tunas Harapan</span>',
            'subtitle' => 'Membentuk Generasi Unggul & Berakhlak',
            'description' => 'Bersama kita menciptakan generasi muda yang berakhlakul mulia, berprestasi, dan siap menghadapi tantangan masa depan dengan pendidikan berkualitas.',
            'primary_button_text' => 'Daftar Sekarang',
            'primary_button_url' => '#contact',
            'secondary_button_text' => 'Lihat Video Profil',
            'secondary_button_url' => 'https://youtu.be/QY29k9_uCII?si=eQtnMSsiwiZbAAIt',
            'background_color' => '#1e3a8a',
            'text_color' => '#ffffff',
            'is_active' => true,
            'sort_order' => 1
        ]);
    }
}
