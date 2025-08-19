<?php
namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;

// --- Daftarkan semua widget yang akan ditampilkan di Dashboard ---
use App\Filament\Widgets\DashboardReportWidget;
use App\Filament\Widgets\DuesStatsWidget;
use App\Filament\Widgets\KasBulananChart; // <-- Pastikan ini di-import

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->font('Poppins')
            ->brandName('KAS ORGANISASI')
            ->favicon(asset('images/favicon.png'))
            ->colors([
                'primary' => Color::Emerald,
            ])

                        

            
            // [DIPERBAIKI] Mengatur urutan grup navigasi di sidebar
            ->navigationGroups([
                NavigationGroup::make()
                     ->label('Kas'),
                NavigationGroup::make()
                    ->label('Laporan'),
                NavigationGroup::make()
                     ->label('Pelindung'),
                NavigationGroup::make()
                     ->label('Peran'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // [DIUPDATE] Mendaftarkan semua widget, termasuk chart baru
            ->widgets([
                DuesStatsWidget::class,
                KasBulananChart::class, // <-- Chart baru ditambahkan di sini
            ])
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
            ])
            ->plugins([
                 \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
