<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController as PublicArticleController;
use App\Http\Controllers\AdminTwoFactorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/articles', [PublicArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article}', [PublicArticleController::class, 'show'])->name('articles.show');

// Admin 2FA routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/verify-2fa', [AdminTwoFactorController::class, 'show'])->name('admin.verify-2fa');
    Route::post('/admin/verify-2fa', [AdminTwoFactorController::class, 'verify'])->name('admin.verify-2fa.submit');
    Route::post('/admin/resend-2fa', [AdminTwoFactorController::class, 'resend'])->name('admin.resend-2fa');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
