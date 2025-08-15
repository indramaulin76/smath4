<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Profile::create([
            'school_name' => 'SMA Tunas Harapan',
            'description' => 'Sekolah Menengah Atas yang berfokus pada pendidikan berkualitas dan pembentukan karakter.',
            'about' => 'SMA swasta ini didirikan pertama kali pada tahun 1987 oleh Yayasan Danar Dana Swadharma. Sekarang SMA Tunas Harapan memakai panduan kurikulum belajar SMA 2013 IPS dan sedang menuju penerapan kurikulum merdeka. kami percaya bahwa setiap anak memiliki potensi besar untuk menjadi pemimpin masa depan. Melalui pendidikan yang bermakna, pembinaan karakter, dan nilai-nilai keagamaan, kami membentuk pribadi yang unggul, percaya diri, dan bertanggung jawab. Lebih dari sekadar sekolah, kami adalah tempat tumbuh, belajar, dan menginspirasi. Mari wujudkan masa depan gemilang bersama kami.',
            'vision' => 'Menciptakan generasi muda yang berakhlakul mulia, unggul dalam IMTAQ, berkarakter dan santun dalam perilaku, berPrestasi dalam bidang akademik dan IPTEK',
            'mission' => 'Memberikan pendidikan berkualitas tinggi yang mengembangkan potensi akademik, karakter, dan spiritual siswa melalui pembelajaran inovatif dan pembinaan karakter yang komprehensif.',
            'established_year' => 1987,
            'principal_name' => 'Kepala Sekolah SMA Tunas Harapan',
            'gallery' => [],
            'sections' => [
                'IPA' => 'Ilmu Pengetahuan Alam',
                'IPS' => 'Ilmu Pengetahuan Sosial'
            ],
            'achievements' => [
                'Juara 1 Olimpiade Matematika Tingkat Provinsi',
                'Akreditasi A dari Kemendikbud',
                'Sekolah Adiwiyata Tingkat Kabupaten'
            ],
            'facilities' => [
                'Laboratorium Komputer',
                'Laboratorium IPA',
                'Perpustakaan',
                'Ruang Multimedia',
                'Lapangan Olahraga'
            ],
            'address' => 'Jl. Pendidikan No. 123, Kota Anda',
            'phone' => '(021) 12345678',
            'email' => 'info@smatunasharapan.sch.id',
            'website' => 'https://smatunasharapan.sch.id'
        ]);
    }
}
