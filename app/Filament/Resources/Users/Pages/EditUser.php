<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggleBlock')
                ->label(fn (): string => $this->getUserRecord()->isBlocked() ? 'Unblock user' : 'Block user')
                ->color(fn (): string => $this->getUserRecord()->isBlocked() ? 'success' : 'danger')
                ->requiresConfirmation()
                ->disabled(fn (): bool => Auth::id() === $this->getUserRecord()->id)
                ->action(function (): void {
                    $user = $this->getUserRecord();

                    $user->update([
                        'is_blocked' => ! $user->isBlocked(),
                    ]);
                }),
        ];
    }

    protected function getUserRecord(): User
    {
        /** @var User $record */
        $record = $this->getRecord();

        return $record;
    }
}
