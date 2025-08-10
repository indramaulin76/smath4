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
        Schema::table('articles', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->after('content'); // Ringkasan artikel
            $table->json('gallery')->nullable()->after('image'); // Foto tambahan
            $table->json('links')->nullable()->after('gallery'); // Tombol link
            $table->boolean('is_published')->default(true)->after('links'); // Status publikasi
            $table->timestamp('published_at')->nullable()->after('is_published'); // Waktu publikasi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['excerpt', 'gallery', 'links', 'is_published', 'published_at']);
        });
    }
};
