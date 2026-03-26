<?php

namespace App\Filament\Resources\Dictionary\UserRoles\Tables;

use App\Filament\Resources\Dictionary\UserRoles\UserRoleResource;
use App\Models\Dictionary\UserRole;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserRolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Role name')
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
            ])
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('name')
            ->recordUrl(fn (UserRole $record): string => UserRoleResource::getUrl('edit', ['record' => $record]));
    }
}
