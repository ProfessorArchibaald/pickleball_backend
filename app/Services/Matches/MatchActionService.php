<?php

namespace App\Services\Matches;

use App\Models\GameMatch;
use App\Models\MatchPoint;

class MatchActionService
{
    public function __construct(
        private readonly GetActualMatchPointService $getActualMatchPointService,
    ) {}

    public function handle(GameMatch $match): MatchPoint
    {
        $matchPoint = $this->getActualMatchPointService
            ->get($match)
            ->loadMissing(['servePlayer', 'winPointPlayer']);

        if ($matchPoint->win_point_player_id === null) {
            return $matchPoint;
        }

        if ($matchPoint->serve_player_id === $matchPoint->win_point_player_id) {
            $scoreColumn = $matchPoint->servePlayer->team === 1 ? 'team1_score' : 'team2_score';

            $matchPoint->increment($scoreColumn);
            $matchPoint->refresh();

            return $this->createNextPoint(
                match: $match,
                servePlayerId: $matchPoint->serve_player_id,
                team1Score: $matchPoint->team1_score,
                team2Score: $matchPoint->team2_score,
            );
        }

        return $this->createNextPoint(
            match: $match,
            servePlayerId: $matchPoint->win_point_player_id,
            team1Score: $matchPoint->team1_score,
            team2Score: $matchPoint->team2_score,
        );
    }

    private function createNextPoint(
        GameMatch $match,
        int $servePlayerId,
        int $team1Score,
        int $team2Score,
    ): MatchPoint {
        return $match->matchPoints()->create([
            'serve_player_id' => $servePlayerId,
            'team1_score' => $team1Score,
            'team2_score' => $team2Score,
            'win_point_player_id' => null,
        ]);
    }
}
