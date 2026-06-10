<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected static string $layout = 'filament.pages.auth.login-layout';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->autocomplete()
                    ->autofocus()
                    ->extraInputAttributes(['placeholder' => 'admin@tokoonline.test']),
                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->required()
                    ->extraInputAttributes(['placeholder' => 'Masukkan kata sandi']),
                Checkbox::make('remember')
                    ->label('Ingat saya'),
            ])
            ->statePath('data');
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
