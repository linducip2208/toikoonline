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
