<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('First name')
                    ->required()
                    ->maxLength(255)
                    ->autocomplete('given-name'),
                TextInput::make('last_name')
                    ->label('Last name')
                    ->required()
                    ->maxLength(255)
                    ->autocomplete('family-name'),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->autocomplete('email'),
                Select::make('role_id')
                    ->label('Role')
                    ->relationship('role', 'name')
                    ->required()
                    ->preload(),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->rule(Password::default())
                    ->showAllValidationMessages()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->same('password_confirmation'),
                TextInput::make('password_confirmation')
                    ->label('Password confirmation')
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required(fn (Get $get, string $operation): bool => $operation === 'create' || filled($get('password')))
                    ->visible(fn (Get $get, string $operation): bool => $operation === 'create' || filled($get('password')))
                    ->dehydrated(false),
            ]);
    }
}
