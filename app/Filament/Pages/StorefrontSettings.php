<?php

namespace App\Filament\Pages;

use App\Models\BusinessSetting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;

class StorefrontSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = '🏪 Toko';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.storefront-settings';
    protected static ?string $title = 'Pengaturan Storefront';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = BusinessSetting::where('type', 'storefront')->pluck('value', 'key');

        $this->form->fill([
            'flash_deal' => (bool) ($settings['flash_deal'] ?? '1'),
            'todays_deal' => (bool) ($settings['todays_deal'] ?? '1'),
            'featured_products' => (bool) ($settings['featured_products'] ?? '1'),
            'featured_categories' => (bool) ($settings['featured_categories'] ?? '1'),
            'best_selling' => (bool) ($settings['best_selling'] ?? '1'),
            'new_products' => (bool) ($settings['new_products'] ?? '1'),
            'category_products' => (bool) ($settings['category_products'] ?? '1'),
            'coupon_system' => (bool) ($settings['coupon_system'] ?? '0'),
            'home_banner1' => (bool) ($settings['home_banner1'] ?? '1'),
            'home_banner2' => (bool) ($settings['home_banner2'] ?? '1'),
            'home_banner3' => (bool) ($settings['home_banner3'] ?? '1'),
            'top_brands' => (bool) ($settings['top_brands'] ?? '1'),
            'newsletter' => (bool) ($settings['newsletter'] ?? '1'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Section Homepage')
                    ->description('Aktifkan / nonaktifkan section yang tampil di halaman depan.')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Toggle::make('flash_deal')->label('Flash Deal')->helperText('Flash sale + countdown'),
                            Forms\Components\Toggle::make('todays_deal')->label('Deal Hari Ini')->helperText('Produk deal harian'),
                            Forms\Components\Toggle::make('featured_products')->label('Produk Unggulan')->helperText('Grid produk featured'),
                            Forms\Components\Toggle::make('featured_categories')->label('Kategori Populer')->helperText('Grid kategori'),
                            Forms\Components\Toggle::make('best_selling')->label('Terlaris')->helperText('Produk terlaris'),
                            Forms\Components\Toggle::make('new_products')->label('Produk Terbaru')->helperText('Produk terbaru'),
                            Forms\Components\Toggle::make('category_products')->label('Produk per Kategori')->helperText('Per kategori'),
                            Forms\Components\Toggle::make('coupon_system')->label('Section Kupon')->helperText('Promo kupon'),
                            Forms\Components\Toggle::make('home_banner1')->label('Banner Section 1')->helperText('Setelah deal hari ini'),
                            Forms\Components\Toggle::make('home_banner2')->label('Banner Section 2')->helperText('Setelah unggulan'),
                            Forms\Components\Toggle::make('home_banner3')->label('Banner Section 3')->helperText('Setelah terlaris'),
                            Forms\Components\Toggle::make('top_brands')->label('Brand Teratas')->helperText('Grid brand'),
                            Forms\Components\Toggle::make('newsletter')->label('Newsletter')->helperText('Form subscribe'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            BusinessSetting::updateOrCreate(
                ['type' => 'storefront', 'key' => $key],
                ['value' => $value ? '1' : '0']
            );
        }

        Notification::make()
            ->title('Pengaturan storefront berhasil disimpan!')
            ->success()
            ->send();
    }
}
