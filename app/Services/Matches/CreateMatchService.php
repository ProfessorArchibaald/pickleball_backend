<?php

namespace App\Services\Matches;

use App\Models\GameMatch;

class CreateMatchService
{
    /**
     * @param  array{game_type_id: int}  $attributes
     */
    public function create(array $attributes): GameMatch
    {
        $match = GameMatch::query()->create([
            'game_type_id' => $attributes['game_type_id'],
        ]);

        return $match->load('gameType');
    }
}
