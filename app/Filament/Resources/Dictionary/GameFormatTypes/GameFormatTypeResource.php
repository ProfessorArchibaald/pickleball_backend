<?php

namespace App\Filament\Resources\Dictionary\GameFormatTypes;

use App\Filament\Resources\Dictionary\GameFormatTypes\Pages\CreateGameFormatType;
use App\Filament\Resources\Dictionary\GameFormatTypes\Pages\EditGameFormatType;
use App\Filament\Resources\Dictionary\GameFormatTypes\Pages\ListGameFormatTypes;
use App\Filament\Resources\Dictionary\GameFormatTypes\Schemas\GameFormatTypeForm;
use App\Filament\Resources\Dictionary\GameFormatTypes\Tables\GameFormatTypesTable;
use App\Models\Dictionary\Game\GameFormatType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GameFormatTypeResource extends Resource
{
    protected static ?string $model = GameFormatType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Game Format Types';

    protected static string|\UnitEnum|null $navigationGroup = 'Dictionaries';

    protected static ?string $modelLabel = 'game format type';

    protected static ?string $pluralModelLabel = 'game format types';

    protected static ?int $navigationSort = 996;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return GameFormatTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GameFormatTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGameFormatTypes::route('/'),
            'create' => CreateGameFormatType::route('/create'),
            'edit' => EditGameFormatType::route('/{record}/edit'),
        ];
    }
}
