<?php

namespace App\Filament\Resources\GameMatches\Tables;

use App\Filament\Resources\GameMatches\GameMatchResource;
use App\Models\GameMatch;
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
                TextColumn::make('finished_at')
                    ->label('Finished at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Duration (seconds)')
                    ->sortable(),
            ])
            ->filters([])
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('id')
            ->recordUrl(fn (GameMatch $record): string => GameMatchResource::getUrl('view', ['record' => $record]));
    }
}
