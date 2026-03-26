<?php

namespace App\Filament\Resources\GameMatches\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GameMatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->label('ID')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('game_type_id')
                    ->label('Game type')
                    ->relationship('gameType', 'name')
                    ->disabled()
                    ->dehydrated(false)
                    ->preload(),
                Select::make('game_format_id')
                    ->label('Game format')
                    ->relationship('gameFormat', 'name')
                    ->disabled()
                    ->dehydrated(false)
                    ->preload(),
                DateTimePicker::make('created_at')
                    ->label('Created at')
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('finished_at')
                    ->label('Finished at')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('duration')
                    ->label('Duration (seconds)')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
