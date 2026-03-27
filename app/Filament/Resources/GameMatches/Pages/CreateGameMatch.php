<?php

namespace App\Filament\Resources\GameMatches\Pages;

use App\Filament\Resources\GameMatches\GameMatchResource;
use App\Services\Matches\CreateMatchService;
use App\Services\Matches\StoreMatchDataFactory;
use App\Services\Matches\StoreMatchInputValidator;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateGameMatch extends CreateRecord
{
    protected static string $resource = GameMatchResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $creatorUserId = (int) auth()->id();
        $validatedData = app(StoreMatchInputValidator::class)->validate(
            data: $data,
            creatorUserId: $creatorUserId,
            playersField: 'player_user_ids',
            playerUserIdField: 'player_user_ids.*',
        );

        return app(CreateMatchService::class)->create(
            app(StoreMatchDataFactory::class)->fromFilamentPayload($validatedData, $creatorUserId),
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
