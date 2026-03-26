<?php

namespace App\Filament\Resources\Dictionary\GameFormatTypes\Pages;

use App\Filament\Resources\Dictionary\GameFormatTypes\GameFormatTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGameFormatTypes extends ListRecords
{
    protected static string $resource = GameFormatTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
