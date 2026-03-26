<?php

namespace App\Filament\Resources\Dictionary\UserRoles\Pages;

use App\Filament\Resources\Dictionary\UserRoles\UserRoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserRole extends CreateRecord
{
    protected static string $resource = UserRoleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
