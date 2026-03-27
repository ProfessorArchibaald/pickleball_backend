<?php

namespace App\Services\Matches;

use App\Data\Matches\StoreMatchData;

class StoreMatchDataFactory
{
    /**
     * @param  array<string, mixed>  $validatedData
     */
    public function fromApiPayload(array $validatedData, int $creatorUserId): StoreMatchData
    {
        return new StoreMatchData(
            gameTypeId: (int) $validatedData['game_type_id'],
            gameFormatId: (int) $validatedData['game_format_id'],
            playerUserIds: collect($validatedData['players'])
                ->pluck('user_id')
                ->map(fn (mixed $userId): int => (int) $userId)
                ->all(),
            creatorUserId: $creatorUserId,
        );
    }

    /**
     * @param  array<string, mixed>  $validatedData
     */
    public function fromFilamentPayload(array $validatedData, int $creatorUserId): StoreMatchData
    {
        return new StoreMatchData(
            gameTypeId: (int) $validatedData['game_type_id'],
            gameFormatId: (int) $validatedData['game_format_id'],
            playerUserIds: collect($validatedData['player_user_ids'])
                ->map(fn (mixed $userId): int => (int) $userId)
                ->all(),
            creatorUserId: $creatorUserId,
        );
    }
}
