<?php

namespace App\Filament\Resources\Dictionary\GameTypes\Pages;

use App\Filament\Resources\Dictionary\GameTypes\GameTypeResource;
use Filament\Resources\Pages\EditRecord;

class EditGameType extends EditRecord
{
    protected static string $resource = GameTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
