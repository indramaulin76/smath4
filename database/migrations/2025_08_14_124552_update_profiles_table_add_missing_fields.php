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
            // Tambah field yang belum ada
            if (!Schema::hasColumn('profiles', 'school_name')) {
                $table->string('school_name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('profiles', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('profiles', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('profiles', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('profiles', 'website')) {
                $table->string('website')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['school_name', 'address', 'phone', 'email', 'website']);
        });
    }
};
