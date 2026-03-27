<?php

namespace App\Filament\Resources\Dictionary\GameFormats\Tables;

use App\Filament\Resources\Dictionary\GameFormats\GameFormatResource;
use App\Models\Dictionary\Game\GameFormat;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GameFormatsTable
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
                TextColumn::make('number_of_players')
                    ->label('Number of players')
                    ->sortable(),
            ])
            ->filters([])
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('id')
            ->recordUrl(fn (GameFormat $record): string => GameFormatResource::getUrl('edit', ['record' => $record]));
    }
}
