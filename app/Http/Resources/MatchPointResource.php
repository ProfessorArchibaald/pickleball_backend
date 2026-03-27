<?php

namespace App\Http\Resources;

use App\Models\MatchPoint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/** @mixin MatchPoint */
#[OA\Schema(
    schema: 'MatchPointData',
    required: ['id', 'match_id', 'serve_player_id', 'team1_score', 'team2_score', 'win_point_player_id', 'created_at', 'updated_at'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'match_id', type: 'integer', example: 1),
        new OA\Property(property: 'serve_player_id', type: 'integer', example: 12),
        new OA\Property(property: 'team1_score', type: 'integer', example: 5),
        new OA\Property(property: 'team2_score', type: 'integer', example: 3),
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
        return [
            'id' => $this->id,
            'match_id' => $this->match_id,
            'serve_player_id' => $this->serve_player_id,
            'team1_score' => $this->team1_score,
            'team2_score' => $this->team2_score,
            'win_point_player_id' => $this->win_point_player_id,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
