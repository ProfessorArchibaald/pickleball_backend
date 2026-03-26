<?php

namespace App\Filament\Resources\Dictionary\GameTypes\Pages;

use App\Filament\Resources\Dictionary\GameTypes\GameTypeResource;
use Filament\Resources\Pages\ListRecords;

class ListGameTypes extends ListRecords
{
    protected static string $resource = GameTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
