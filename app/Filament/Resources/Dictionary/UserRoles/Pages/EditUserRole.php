<?php

namespace App\Filament\Resources\Dictionary\UserRoles\Pages;

use App\Filament\Resources\Dictionary\UserRoles\UserRoleResource;
use App\Models\Dictionary\UserRole;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserRole extends EditRecord
{
    protected static string $resource = UserRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (): bool => $this->getUserRoleRecord()->isDefaultRole()),
        ];
    }

    protected function getUserRoleRecord(): UserRole
    {
        /** @var UserRole $record */
        $record = $this->getRecord();

        return $record;
    }
}
