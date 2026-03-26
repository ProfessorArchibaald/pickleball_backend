<?php

namespace App\Filament\Resources\Dictionary\UserRoles\Pages;

use App\Filament\Resources\Dictionary\UserRoles\UserRoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserRoles extends ListRecords
{
    protected static string $resource = UserRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
