<?php

namespace App\Filament\Resources\Dictionary\UserRoles\Schemas;

use App\Models\Dictionary\UserRole;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserRoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Role name')
                    ->required()
                    ->disabled(fn (?UserRole $record): bool => $record?->isDefaultRole() ?? false)
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
