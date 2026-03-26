<?php

namespace App\Filament\Resources\Dictionary\GameFormats\Pages;

use App\Filament\Resources\Dictionary\GameFormats\GameFormatResource;
use Filament\Resources\Pages\ListRecords;

class ListGameFormats extends ListRecords
{
    protected static string $resource = GameFormatResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
