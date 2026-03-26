<?php

namespace App\Filament\Resources\Dictionary\GameFormatTypes\Tables;

use App\Filament\Resources\Dictionary\GameFormatTypes\GameFormatTypeResource;
use App\Models\Dictionary\Game\GameFormatType;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GameFormatTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('gameType.name')
                    ->label('Game type')
                    ->sortable(),
                TextColumn::make('gameFormat.name')
                    ->label('Game format')
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
            ->filters([])
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('id')
            ->recordUrl(fn (GameFormatType $record): string => GameFormatTypeResource::getUrl('edit', ['record' => $record]));
    }
}
