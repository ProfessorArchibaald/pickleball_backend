<?php

namespace App\Http\Resources;

use App\Models\MatchPlayer;
use App\Models\MatchPoint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/** @mixin MatchPoint */
#[OA\Schema(
    schema: 'MatchPointTeamData',
    required: ['user_id', 'player_id', 'team_score'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 7),
        new OA\Property(property: 'player_id', type: 'integer', example: 12),
        new OA\Property(property: 'team_score', type: 'integer', example: 5),
    ],
    type: 'object',
)]
class MatchPointTeamDataSchema {}

/** @mixin MatchPoint */
#[OA\Schema(
    schema: 'MatchPointData',
    required: ['id', 'match_id', 'serve_player_id', 'team_one', 'team_two', 'win_point_player_id', 'created_at', 'updated_at'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'match_id', type: 'integer', example: 1),
        new OA\Property(property: 'serve_player_id', type: 'integer', example: 12),
        new OA\Property(property: 'team_one', ref: '#/components/schemas/MatchPointTeamData'),
        new OA\Property(property: 'team_two', ref: '#/components/schemas/MatchPointTeamData'),
        new OA\Property(property: 'win_point_player_id', type: 'integer', example: 13, nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object',
)]
class MatchPointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->resource->loadMissing('gameMatch.matchPlayers.user');

        $teamOnePlayer = $this->resource->gameMatch->matchPlayers->firstWhere('team', 1);
        $teamTwoPlayer = $this->resource->gameMatch->matchPlayers->firstWhere('team', 2);

        return [
            'id' => $this->id,
            'match_id' => $this->match_id,
            'serve_player_id' => $this->serve_player_id,
            'team_one' => $this->formatTeam($teamOnePlayer, $this->team1_score),
            'team_two' => $this->formatTeam($teamTwoPlayer, $this->team2_score),
            'win_point_player_id' => $this->win_point_player_id,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    /**
     * @return array{user_id:int|null, player_id:int|null, team_score:int}
     */
    private function formatTeam(?MatchPlayer $matchPlayer, int $teamScore): array
    {
        return [
            'user_id' => $matchPlayer?->user_id,
            'player_id' => $matchPlayer?->id,
            'team_score' => $teamScore,
        ];
    }
}
