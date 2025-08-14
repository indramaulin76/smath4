<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::create([
            'title' => 'Pendidikan Berkualitas',
            'description' => 'Memberikan pendidikan berkualitas tinggi dengan kurikulum yang komprehensif dan metode pembelajaran yang inovatif.'
        ]);

        Service::create([
            'title' => 'Fasilitas Lengkap',
            'description' => 'Dilengkapi dengan fasilitas modern seperti laboratorium komputer, perpustakaan, dan ruang kelas yang nyaman.'
        ]);

        Service::create([
            'title' => 'Tenaga Pendidik Profesional',
            'description' => 'Didukung oleh tenaga pendidik yang profesional, berpengalaman, dan memiliki dedikasi tinggi.'
        ]);

        Service::create([
            'title' => 'Ekstrakurikuler Beragam',
            'description' => 'Menyediakan berbagai kegiatan ekstrakurikuler untuk mengembangkan bakat dan minat siswa.'
        ]);
    }
}
