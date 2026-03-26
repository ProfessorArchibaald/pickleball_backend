<?php

namespace App\Filament\Resources\Dictionary\GameFormatTypes\Pages;

use App\Filament\Resources\Dictionary\GameFormatTypes\GameFormatTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGameFormatType extends EditRecord
{
    protected static string $resource = GameFormatTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
