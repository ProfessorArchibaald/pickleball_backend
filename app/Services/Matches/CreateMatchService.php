<?php

namespace App\Services\Matches;

use App\Data\Matches\StoreMatchData;
use App\Models\GameMatch;

class CreateMatchService
{
    public function create(StoreMatchData $data): GameMatch
    {
        $match = GameMatch::query()->create([
            'game_type_id' => $data->gameTypeId,
        ]);

        return $match->load('gameType');
    }
}
