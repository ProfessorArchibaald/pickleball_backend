<?php

namespace App\Filament\Resources\Dictionary\GameFormats\Tables;

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
            ])
            ->filters([])
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('id')
            ->recordUrl(null);
    }
}
