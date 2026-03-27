<?php

namespace App\Filament\Resources\GameMatches\Schemas;

use App\Models\Dictionary\Game\GameFormat;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class GameMatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            ...self::createFields(),
            ...self::editFields(),
        ]);
    }

    /**
     * @return array<int, mixed>
     */
    private static function createFields(): array
    {
        return [
            Select::make('game_type_id')
                ->label('Game type')
                ->relationship('gameType', 'name')
                ->required()
                ->live()
                ->afterStateUpdated(function (Set $set): void {
                    $set('game_format_id', null);
                    $set('player_user_ids', []);
                })
                ->hiddenOn('edit')
                ->preload(),
            Select::make('game_format_id')
                ->label('Game format')
                ->required()
                ->options(function (Get $get): array {
                    $gameTypeId = $get('game_type_id');

                    if (blank($gameTypeId)) {
                        return [];
                    }

                    return GameFormat::query()
                        ->whereHas('gameFormatTypes', fn (Builder $query) => $query->where('game_type_id', $gameTypeId))
                        ->orderBy('id')
                        ->pluck('name', 'id')
                        ->all();
                })
                ->afterStateUpdated(function (Set $set): void {
                    $set('player_user_ids', []);
                })
                ->hiddenOn('edit')
                ->disabled(fn (Get $get): bool => blank($get('game_type_id')))
                ->preload()
                ->live(),
            Select::make('player_user_ids')
                ->label('Players')
                ->multiple()
                ->required()
                ->searchable()
                ->helperText('Select players to add to the match. The current authenticated user is added automatically as the creator.')
                ->options(
                    fn (): array => User::query()
                        ->whereKeyNot(auth()->id())
                        ->orderBy('name')
                        ->orderBy('last_name')
                        ->get(['id', 'name', 'last_name'])
                        ->mapWithKeys(fn (User $user): array => [
                            $user->id => $user->fullName(),
                        ])
                        ->all(),
                )
                ->hiddenOn('edit')
                ->disabled(fn (Get $get): bool => blank($get('game_format_id')))
                ->preload(),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    private static function editFields(): array
    {
        return [
            TextInput::make('id')
                ->label('ID')
                ->hiddenOn('create')
                ->disabled()
                ->dehydrated(false),
            TextInput::make('game_type_name')
                ->label('Game type')
                ->formatStateUsing(fn ($record): ?string => $record?->gameType?->name)
                ->hiddenOn('create')
                ->disabled()
                ->dehydrated(false),
            TextInput::make('game_format_name')
                ->label('Game format')
                ->formatStateUsing(fn ($record): ?string => $record?->gameFormat?->name)
                ->hiddenOn('create')
                ->disabled()
                ->dehydrated(false),
            DateTimePicker::make('created_at')
                ->label('Created at')
                ->hiddenOn('create')
                ->disabled()
                ->dehydrated(false),
            DateTimePicker::make('finished_at')
                ->label('Finished at')
                ->hiddenOn('create')
                ->disabled()
                ->dehydrated(false),
            TextInput::make('duration')
                ->label('Duration (seconds)')
                ->hiddenOn('create')
                ->numeric()
                ->disabled()
                ->dehydrated(false),
        ];
    }
}
