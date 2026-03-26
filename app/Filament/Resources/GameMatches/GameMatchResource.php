<?php

namespace App\Filament\Resources\GameMatches;

use App\Filament\Resources\GameMatches\Pages\ListGameMatches;
use App\Filament\Resources\GameMatches\Tables\GameMatchesTable;
use App\Models\GameMatch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GameMatchResource extends Resource
{
    protected static ?string $model = GameMatch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Matches';

    protected static ?string $modelLabel = 'match';

    protected static ?string $pluralModelLabel = 'matches';

    public static function table(Table $table): Table
    {
        return GameMatchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGameMatches::route('/'),
        ];
    }
}
