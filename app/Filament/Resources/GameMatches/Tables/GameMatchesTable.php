<?php

namespace App\Filament\Resources\GameMatches\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GameMatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('game_type_id')
                    ->label('Game type ID')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('finished_at')
                    ->label('Finished at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('id')
            ->recordUrl(null);
    }
}
