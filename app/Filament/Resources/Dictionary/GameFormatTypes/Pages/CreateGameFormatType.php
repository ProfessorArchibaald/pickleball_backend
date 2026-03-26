<?php

namespace App\Filament\Resources\Dictionary\GameFormatTypes\Pages;

use App\Filament\Resources\Dictionary\GameFormatTypes\GameFormatTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGameFormatType extends CreateRecord
{
    protected static string $resource = GameFormatTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
