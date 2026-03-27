<?php

namespace App\Filament\Resources\Dictionary\GameFormats\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GameFormatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('number_of_players')
                    ->label('Number of players')
                    ->required()
                    ->numeric()
                    ->rules(['integer', 'min:1']),
            ]);
    }
}
