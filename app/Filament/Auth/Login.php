<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function registerAction(): Action
    {
        return Action::make('register')
            ->label(__('filament-panels::auth/pages/login.actions.register.label'))
            ->color('gray')
            ->url(filament()->getRegistrationUrl());
    }

    public function getSubheading(): string|Htmlable|null
    {
        return null;
    }

    protected function getFormActions(): array
    {
        $actions = [
            $this->getAuthenticateFormAction(),
        ];

        if (User::query()->doesntExist()) {
            $actions[] = $this->registerAction();
        }

        return $actions;
    }
}
