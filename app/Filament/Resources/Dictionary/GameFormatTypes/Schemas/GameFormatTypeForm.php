<?php

namespace App\Filament\Resources\Dictionary\GameFormatTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class GameFormatTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('game_type_id')
                    ->label('Game type')
                    ->relationship('gameType', 'name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->preload(),
                Select::make('game_format_id')
                    ->label('Game format')
                    ->relationship('gameFormat', 'name')
                    ->required()
                    ->preload(),
            ]);
    }
}
