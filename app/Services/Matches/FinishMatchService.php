<?php

namespace App\Services\Matches;

use App\Models\GameMatch;

class FinishMatchService
{
    public function finish(GameMatch $match): GameMatch
    {
        if ($match->finished_at === null) {
            $finishedAt = now();

            $match->forceFill([
                'finished_at' => $finishedAt,
                'duration' => (int) $match->created_at->diffInSeconds($finishedAt),
            ])->save();
        }

        return $match->fresh(['gameType', 'gameFormat']);
    }
}
