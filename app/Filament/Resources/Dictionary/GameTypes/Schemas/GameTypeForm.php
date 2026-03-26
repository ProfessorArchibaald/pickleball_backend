<?php

namespace App\Filament\Resources\Dictionary\GameTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GameTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
