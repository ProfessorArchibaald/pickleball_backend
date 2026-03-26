<?php

namespace App\Filament\Resources\Dictionary\GameTypes;

use App\Filament\Resources\Dictionary\GameTypes\Pages\EditGameType;
use App\Filament\Resources\Dictionary\GameTypes\Pages\ListGameTypes;
use App\Filament\Resources\Dictionary\GameTypes\Schemas\GameTypeForm;
use App\Filament\Resources\Dictionary\GameTypes\Tables\GameTypesTable;
use App\Models\Dictionary\Game\GameType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GameTypeResource extends Resource
{
    protected static ?string $model = GameType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Game Types';

    protected static string|\UnitEnum|null $navigationGroup = 'Dictionaries';

    protected static ?string $modelLabel = 'game type';

    protected static ?string $pluralModelLabel = 'game types';

    protected static ?int $navigationSort = 998;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GameTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GameTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGameTypes::route('/'),
            'edit' => EditGameType::route('/{record}/edit'),
        ];
    }
}
