<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Article;
use App\Models\Teacher;
use App\Models\Service;
use App\Models\HeroSection;

class SchoolOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('📰 Total Artikel', Article::count())
                ->description('Artikel & berita yang telah dipublikasikan')
                ->descriptionIcon('heroicon-o-newspaper')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
                
            Stat::make('👨‍🏫 Data Guru', Teacher::count())
                ->description('Guru yang terdaftar di sistem')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('info')
                ->chart([15, 18, 20, 22, 25, 28, Teacher::count()]),
                
            Stat::make('⚙️ Layanan Aktif', Service::count())
                ->description('Layanan sekolah yang tersedia')
                ->descriptionIcon('heroicon-o-cog-6-tooth')
                ->color('warning')
                ->chart([3, 5, 6, 7, Service::count(), Service::count(), Service::count()]),
                
            Stat::make('🏠 Halaman Utama', HeroSection::where('is_active', true)->count())
                ->description('Halaman utama yang aktif')
                ->descriptionIcon('heroicon-o-home')
                ->color('primary')
                ->chart([1, 1, 1, 1, 1, 1, HeroSection::where('is_active', true)->count()]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 2; // 2 kolom untuk tampilan yang lebih rapi
    }
}
