<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Navigation\NavigationGroup;
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
            ->path('admin')
            ->brandName('TokoOnline')
            ->login()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('15.5rem')
            ->collapsedSidebarWidth('4rem')
            ->topNavigation()
            ->navigationGroups([
                NavigationGroup::make('🏪 Toko')->collapsed(false),
                NavigationGroup::make('📦 Katalog')->collapsed(false),
                NavigationGroup::make('🛒 Pesanan')->collapsed(false),
                NavigationGroup::make('💳 Pembayaran')->collapsed(true),
                NavigationGroup::make('🚚 Pengiriman')->collapsed(true),
                NavigationGroup::make('👥 Pelanggan')->collapsed(true),
                NavigationGroup::make('🎫 Promo')->collapsed(true),
                NavigationGroup::make('📢 Marketing')->collapsed(true),
                NavigationGroup::make('💰 Keuangan')->collapsed(true),
                NavigationGroup::make('🤝 Afiliasi')->collapsed(true),
                NavigationGroup::make('⚙️ Sistem')->collapsed(true),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\OrderChartWidget::class,
                \App\Filament\Widgets\RecentOrdersWidget::class,
            ])
            ->databaseNotifications()
            ->profile()
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
