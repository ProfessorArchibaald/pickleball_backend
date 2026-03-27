<?php

namespace App\Services\Matches;

use App\Data\Matches\StoreMatchData;
use App\Models\GameMatch;
use Illuminate\Support\Facades\DB;

class CreateMatchService
{
    public function create(StoreMatchData $data): GameMatch
    {
        $match = DB::transaction(function () use ($data): GameMatch {
            $match = GameMatch::query()->create([
                'game_type_id' => $data->gameTypeId,
                'game_format_id' => $data->gameFormatId,
            ]);

            $participantUserIds = [
                ...$data->playerUserIds,
                $data->creatorUserId,
            ];

            $teamAssignments = $this->generateRandomTeams(count($participantUserIds));

            $match->matchPlayers()->createMany(
                collect($participantUserIds)
                    ->values()
                    ->map(fn (int $userId, int $index): array => [
                        'user_id' => $userId,
                        'team' => $teamAssignments[$index],
                        'is_creator' => $userId === $data->creatorUserId,
                    ])
                    ->all(),
            );

            return $match;
        });

        return $match->load(['gameType', 'gameFormat']);
    }

    /**
     * @return array<int, int>
     */
    private function generateRandomTeams(int $playersCount): array
    {
        $smallerTeamSize = intdiv($playersCount, 2);
        $largerTeamSize = $playersCount - $smallerTeamSize;
        $teams = [
            1 => $smallerTeamSize,
            2 => $largerTeamSize,
        ];

        if ($smallerTeamSize !== $largerTeamSize && random_int(0, 1) === 1) {
            $teams = [
                1 => $largerTeamSize,
                2 => $smallerTeamSize,
            ];
        }

        $assignments = [
            ...array_fill(0, $teams[1], 1),
            ...array_fill(0, $teams[2], 2),
        ];

        shuffle($assignments);

        return $assignments;
    }
}
