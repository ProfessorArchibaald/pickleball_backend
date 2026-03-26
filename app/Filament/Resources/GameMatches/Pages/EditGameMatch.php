<?php

namespace App\Filament\Resources\GameMatches\Pages;

use App\Filament\Resources\GameMatches\GameMatchResource;
use App\Models\GameMatch;
use App\Services\Matches\FinishMatchService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditGameMatch extends EditRecord
{
    protected static string $resource = GameMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('finishMatch')
                ->label('Finish Match')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(fn (): bool => $this->getGameMatchRecord()->finished_at !== null)
                ->action(function (FinishMatchService $finishMatchService): void {
                    $this->record = $finishMatchService->finish($this->getGameMatchRecord());
                    $this->fillForm();

                    Notification::make()
                        ->title('Match finished')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
        ];
    }

    protected function getGameMatchRecord(): GameMatch
    {
        /** @var GameMatch $record */
        $record = $this->getRecord();

        return $record;
    }
}
