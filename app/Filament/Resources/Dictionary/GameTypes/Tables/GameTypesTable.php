<?php

namespace App\Filament\Resources\Dictionary\GameTypes\Tables;

use App\Filament\Resources\Dictionary\GameTypes\GameTypeResource;
use App\Models\Dictionary\GameType;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GameTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('id')
            ->recordUrl(fn (GameType $record): string => GameTypeResource::getUrl('edit', ['record' => $record]));
    }
}
