<?php

namespace App\Filament\Auth;

use App\Models\Dictionary\UserRole;
use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class Register extends BaseRegister
{
    public function mount(): void
    {
        if (User::query()->exists()) {
            $this->redirect(filament()->getLoginUrl());

            return;
        }

        parent::mount();
    }

    public function register(): ?RegistrationResponse
    {
        abort_if(User::query()->exists(), 404);

        return parent::register();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getLastNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('First name')
            ->required()
            ->maxLength(255)
            ->autocomplete('given-name')
            ->autofocus();
    }

    protected function getLastNameFormComponent(): Component
    {
        return TextInput::make('last_name')
            ->label('Last name')
            ->required()
            ->autocomplete('family-name')
            ->maxLength(255);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $adminRole = UserRole::query()->firstOrCreate([
            'name' => UserRole::ADMIN,
        ]);

        $data['role_id'] = $adminRole->getKey();

        return $data;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Create the first administrator account.';
    }
}
