<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;

class Login extends BaseLogin
{
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

    protected function getViewData(): array
    {
        return [
            'demoAccounts' => [
                ['role' => 'Admin', 'email' => 'admin@tokoonline.test', 'password' => 'password'],
                ['role' => 'Customer', 'email' => 'customer@tokoonline.test', 'password' => 'password'],
            ],
        ];
    }

    protected function getLayoutData(): array
    {
        return array_merge(parent::getLayoutData(), [
            'hasTopbar' => false,
        ]);
    }
}
