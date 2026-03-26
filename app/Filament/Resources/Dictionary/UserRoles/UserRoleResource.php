<?php

namespace App\Filament\Resources\Dictionary\UserRoles;

use App\Filament\Resources\Dictionary\UserRoles\Pages\CreateUserRole;
use App\Filament\Resources\Dictionary\UserRoles\Pages\EditUserRole;
use App\Filament\Resources\Dictionary\UserRoles\Pages\ListUserRoles;
use App\Filament\Resources\Dictionary\UserRoles\Schemas\UserRoleForm;
use App\Filament\Resources\Dictionary\UserRoles\Tables\UserRolesTable;
use App\Models\Dictionary\UserRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserRoleResource extends Resource
{
    protected static ?string $model = UserRole::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'User Roles';

    protected static string|\UnitEnum|null $navigationGroup = 'Dictionaries';

    protected static ?string $modelLabel = 'user role';

    protected static ?string $pluralModelLabel = 'user roles';

    protected static ?int $navigationSort = 999;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UserRoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserRolesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserRoles::route('/'),
            'create' => CreateUserRole::route('/create'),
            'edit' => EditUserRole::route('/{record}/edit'),
        ];
    }
}
