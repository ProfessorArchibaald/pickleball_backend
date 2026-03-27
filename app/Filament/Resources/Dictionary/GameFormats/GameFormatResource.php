<?php

namespace App\Filament\Resources\Dictionary\GameFormats;

use App\Filament\Resources\Dictionary\GameFormats\Pages\EditGameFormat;
use App\Filament\Resources\Dictionary\GameFormats\Pages\ListGameFormats;
use App\Filament\Resources\Dictionary\GameFormats\Schemas\GameFormatForm;
use App\Filament\Resources\Dictionary\GameFormats\Tables\GameFormatsTable;
use App\Models\Dictionary\Game\GameFormat;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GameFormatResource extends Resource
{
    protected static ?string $model = GameFormat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Game Formats';

    protected static string|\UnitEnum|null $navigationGroup = 'Dictionaries';

    protected static ?string $modelLabel = 'game format';

    protected static ?string $pluralModelLabel = 'game formats';

    protected static ?int $navigationSort = 997;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GameFormatForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GameFormatsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGameFormats::route('/'),
            'edit' => EditGameFormat::route('/{record}/edit'),
        ];
    }
}
