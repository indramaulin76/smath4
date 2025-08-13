<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('smath-admin-secure-2025')  // Custom hidden path
            ->login()
            ->brandName('🏫 SMA Tunas Harapan')
            ->brandLogo(asset('images/logo-sma-tunas-harapan.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/logo-sma-tunas-harapan.png'))
            ->colors([
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Red,
                'info' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\WelcomeWidget::class,
                \App\Filament\Widgets\SchoolOverview::class,
                Widgets\AccountWidget::class,
            ])
            ->navigationGroups([
                '🎯 Kelola Konten Website',
                '🏫 Tentang Sekolah', 
                '👥 Data Sekolah',
                '🔐 Manajemen Admin',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                'admin.ip.whitelist',    // IP Whitelist Security
                'admin.rate.limit',     // Rate Limiting
            ])
            ->authMiddleware([
                Authenticate::class,
                'admin.2fa',           // Two-Factor Authentication
            ]);
    }
}
