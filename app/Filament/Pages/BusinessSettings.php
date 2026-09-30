<?php

namespace App\Filament\Pages;

use App\Models\BusinessSetting;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;

class BusinessSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationGroup = '⚙️ Sistem';
    protected static ?int $navigationSort = 0;
    protected static string $view = 'filament.pages.business-settings';
    protected static ?string $title = 'Pengaturan Bisnis';

    public ?array $data = [];
    public string $activeTab = 'general';

    public function mount(): void
    {
        $settings = BusinessSetting::where('type', 'general')->pluck('value', 'key');
        $social = BusinessSetting::where('type', 'social')->pluck('value', 'key');
        $smtp = BusinessSetting::where('type', 'smtp')->pluck('value', 'key');

        $this->form->fill([
            'site_name' => $settings['site_name'] ?? 'TokoOnline',
            'site_description' => $settings['site_description'] ?? '',
            'contact_email' => $settings['contact_email'] ?? '',
            'contact_phone' => $settings['contact_phone'] ?? '',
            'address' => $settings['address'] ?? '',
            'facebook' => $social['facebook'] ?? '',
            'instagram' => $social['instagram'] ?? '',
            'twitter' => $social['twitter'] ?? '',
            'tiktok' => $social['tiktok'] ?? '',
            'youtube' => $social['youtube'] ?? '',
            'smtp_host' => $smtp['smtp_host'] ?? '',
            'smtp_port' => $smtp['smtp_port'] ?? '587',
            'smtp_user' => $smtp['smtp_user'] ?? '',
            'smtp_pass' => $smtp['smtp_pass'] ?? '',
            'smtp_encryption' => $smtp['smtp_encryption'] ?? 'tls',
            'mail_from_address' => $smtp['mail_from_address'] ?? '',
            'mail_from_name' => $smtp['mail_from_name'] ?? '',
            'google_analytics' => $settings['google_analytics'] ?? '',
            'facebook_pixel' => $settings['facebook_pixel'] ?? '',
            'google_recaptcha_site' => $settings['google_recaptcha_site'] ?? '',
            'google_recaptcha_secret' => $settings['google_recaptcha_secret'] ?? '',
            'google_maps_api' => $settings['google_maps_api'] ?? '',
            'invoice_prefix' => $settings['invoice_prefix'] ?? 'INV',
            'invoice_footer' => $settings['invoice_footer'] ?? '',
            'order_code_prefix' => $settings['order_code_prefix'] ?? 'ORD',
            'default_language' => $settings['default_language'] ?? 'id',
            'translation_default' => $settings['translation_default'] ?? 'id',
            'translation_fallback' => $settings['translation_fallback'] ?? 'en',
            'currency_code' => $settings['currency_code'] ?? 'IDR',
            'currency_symbol' => $settings['currency_symbol'] ?? 'Rp',
            'currency_position' => $settings['currency_position'] ?? 'before',
            'tax_enabled' => ($settings['tax_enabled'] ?? '0') === '1',
            'tax_rate_default' => $settings['tax_rate_default'] ?? '0',
            'tax_included' => ($settings['tax_included'] ?? '0') === '1',
            'checkout_guest_enabled' => ($settings['checkout_guest_enabled'] ?? '1') === '1',
            'checkout_min_order' => $settings['checkout_min_order'] ?? '0',
            'checkout_notes_enabled' => ($settings['checkout_notes_enabled'] ?? '1') === '1',
            'warehouse_city_id' => $settings['warehouse_city_id'] ?? '',
            'warehouse_area_id' => $settings['warehouse_area_id'] ?? '',
            'theme_active' => $settings['theme_active'] ?? 'default',
            'ai_api_url' => $settings['ai_api_url'] ?? '',
            'ai_api_key' => $settings['ai_api_key'] ?? '',
            'ai_model' => $settings['ai_model'] ?? 'gpt-4o-mini',
            'maintenance_mode' => ($settings['maintenance_mode'] ?? '0') === '1',
            'seo_meta_title' => $settings['seo_meta_title'] ?? '',
            'seo_meta_description' => $settings['seo_meta_description'] ?? '',
        ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Pengaturan')->tabs([
                Forms\Components\Tabs\Tab::make('Umum')->schema([
                    Forms\Components\TextInput::make('site_name')->label('Nama Situs')->required(),
                    Forms\Components\Textarea::make('site_description')->label('Deskripsi Situs')->rows(2),
                    Forms\Components\TextInput::make('contact_email')->label('Email Kontak')->email(),
                    Forms\Components\TextInput::make('contact_phone')->label('Telepon'),
                    Forms\Components\Textarea::make('address')->label('Alamat')->rows(2),
                ]),

                Forms\Components\Tabs\Tab::make('Sosial Media')->schema([
                    Forms\Components\TextInput::make('facebook')->label('Facebook URL')->url()->prefix('https://'),
                    Forms\Components\TextInput::make('instagram')->label('Instagram URL')->url()->prefix('https://'),
                    Forms\Components\TextInput::make('twitter')->label('Twitter URL')->url()->prefix('https://'),
                    Forms\Components\TextInput::make('tiktok')->label('TikTok URL')->url()->prefix('https://'),
                    Forms\Components\TextInput::make('youtube')->label('YouTube URL')->url()->prefix('https://'),
                ]),

                Forms\Components\Tabs\Tab::make('SMTP Email')->schema([
                    Forms\Components\TextInput::make('smtp_host')->label('SMTP Host')->placeholder('smtp.gmail.com'),
                    Forms\Components\TextInput::make('smtp_port')->label('SMTP Port')->placeholder('587'),
                    Forms\Components\TextInput::make('smtp_user')->label('SMTP Username')->email(),
                    Forms\Components\TextInput::make('smtp_pass')->label('SMTP Password')->password()->revealable(),
                    Forms\Components\Select::make('smtp_encryption')->label('Enkripsi')->options(['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'None']),
                    Forms\Components\TextInput::make('mail_from_address')->label('From Address')->email(),
                    Forms\Components\TextInput::make('mail_from_name')->label('From Name'),
                ]),

                Forms\Components\Tabs\Tab::make('Google & Tracking')->schema([
                    Forms\Components\Textarea::make('google_analytics')->label('Google Analytics ID')->placeholder('G-XXXXXXXXXX'),
                    Forms\Components\Textarea::make('facebook_pixel')->label('Facebook Pixel ID')->placeholder('Paste kode pixel di sini'),
                    Forms\Components\TextInput::make('google_recaptcha_site')->label('reCAPTCHA Site Key'),
                    Forms\Components\TextInput::make('google_recaptcha_secret')->label('reCAPTCHA Secret Key'),
                    Forms\Components\TextInput::make('google_maps_api')->label('Google Maps API Key'),
                ]),

                Forms\Components\Tabs\Tab::make('Invoice & Order')->schema([
                    Forms\Components\TextInput::make('invoice_prefix')->label('Prefix Invoice')->required(),
                    Forms\Components\Textarea::make('invoice_footer')->label('Footer Invoice')->rows(3)->placeholder('Catatan yang muncul di footer invoice'),
                    Forms\Components\TextInput::make('order_code_prefix')->label('Prefix Kode Order')->required(),
                ]),

                Forms\Components\Tabs\Tab::make('Locale & Currency')->schema([
                    Forms\Components\Select::make('default_language')->label('Bahasa Default')->options(['id' => 'Indonesia', 'en' => 'English'])->required(),
                    Forms\Components\Select::make('translation_default')->label('Locale Default (terjemahan)')->options(['id' => 'Indonesia', 'en' => 'English']),
                    Forms\Components\Select::make('translation_fallback')->label('Locale Fallback')->options(['id' => 'Indonesia', 'en' => 'English']),
                    Forms\Components\TextInput::make('currency_code')->label('Kode Mata Uang')->required()->maxLength(3),
                    Forms\Components\TextInput::make('currency_symbol')->label('Simbol Mata Uang')->required()->maxLength(10),
                    Forms\Components\Select::make('currency_position')->label('Posisi Simbol')->options(['before' => 'Depan (Rp100)', 'after' => 'Belakang (100Rp)'])->required(),
                ]),

                Forms\Components\Tabs\Tab::make('Pajak & Checkout')->schema([
                    Forms\Components\Toggle::make('tax_enabled')->label('Pajak Aktif'),
                    Forms\Components\TextInput::make('tax_rate_default')->label('Tarif Pajak Default (%)')->numeric()->minValue(0)->maxValue(100),
                    Forms\Components\Toggle::make('tax_included')->label('Harga Termasuk Pajak'),
                    Forms\Components\Toggle::make('checkout_guest_enabled')->label('Checkout Tamu Diizinkan'),
                    Forms\Components\TextInput::make('checkout_min_order')->label('Minimal Belanja (IDR)')->numeric()->minValue(0),
                    Forms\Components\Toggle::make('checkout_notes_enabled')->label('Catatan Pembeli Aktif'),
                    Forms\Components\TextInput::make('warehouse_city_id')->label('Kota Gudang (ID)'),
                    Forms\Components\TextInput::make('warehouse_area_id')->label('Area Gudang (ID)'),
                ]),

                Forms\Components\Tabs\Tab::make('Tema & AI')->schema([
                    Forms\Components\TextInput::make('theme_active')->label('Tema Aktif')->required()->maxLength(60),
                    Forms\Components\TextInput::make('ai_api_url')->label('AI API URL')->url(),
                    Forms\Components\TextInput::make('ai_api_key')->label('AI API Key')->password()->revealable(),
                    Forms\Components\TextInput::make('ai_model')->label('AI Model')->placeholder('gpt-4o-mini'),
                ]),

                Forms\Components\Tabs\Tab::make('Lainnya')->schema([
                    Forms\Components\Toggle::make('maintenance_mode')->label('Mode Maintenance'),
                    Forms\Components\TextInput::make('seo_meta_title')->label('Meta Title Default')->maxLength(255)->columnSpanFull(),
                    Forms\Components\Textarea::make('seo_meta_description')->label('Meta Description Default')->rows(2)->columnSpanFull(),
                ]),
            ])->activeTab($this->activeTab),
        ])->statePath('data');
    }

    public function updatedActiveTab(): void
    {
        $this->form->fill($this->form->getState());
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $generalKeys = ['site_name', 'site_description', 'contact_email', 'contact_phone', 'address', 'google_analytics', 'facebook_pixel', 'google_recaptcha_site', 'google_recaptcha_secret', 'google_maps_api', 'invoice_prefix', 'invoice_footer', 'order_code_prefix'];
        $socialKeys = ['facebook', 'instagram', 'twitter', 'tiktok', 'youtube'];
        $smtpKeys = ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_encryption', 'mail_from_address', 'mail_from_name'];

        foreach ($data as $key => $value) {
            $type = 'general';
            if (in_array($key, $socialKeys)) $type = 'social';
            if (in_array($key, $smtpKeys)) $type = 'smtp';

            // Normalize toggles to '1'/'0' so typed Settings::get('bool') reads back correctly.
            if (is_bool($value)) $value = $value ? '1' : '0';

            BusinessSetting::updateOrCreate(
                ['type' => $type, 'key' => $key],
                ['value' => $value ?? '']
            );
        }

        Notification::make()->title('Pengaturan berhasil disimpan!')->success()->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Simpan Semua')
                ->icon('heroicon-o-check')
                ->submit('save')
                ->color('primary'),
        ];
    }
}
