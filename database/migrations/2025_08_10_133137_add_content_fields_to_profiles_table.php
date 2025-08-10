<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id'); // Judul/Nama sekolah
            $table->text('description')->nullable()->after('about'); // Deskripsi singkat
            $table->string('featured_image')->nullable()->after('vision'); // Gambar utama
            $table->json('gallery')->nullable()->after('featured_image'); // Gallery foto
            $table->json('sections')->nullable()->after('gallery'); // Konten sections dengan judul dan paragraf
            $table->string('established_year')->nullable()->after('sections'); // Tahun berdiri
            $table->string('principal_name')->nullable()->after('established_year'); // Nama kepala sekolah
            $table->text('mission')->nullable()->after('principal_name'); // Misi sekolah
            $table->json('achievements')->nullable()->after('mission'); // Prestasi sekolah
            $table->json('facilities')->nullable()->after('achievements'); // Fasilitas sekolah
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'title', 
                'description', 
                'featured_image', 
                'gallery', 
                'sections',
                'established_year',
                'principal_name', 
                'mission', 
                'achievements', 
                'facilities'
            ]);
        });
    }
};
