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
            'game_format_id' => $data->gameFormatId,
        ]);

        return $match->load(['gameType', 'gameFormat']);
    }
}
