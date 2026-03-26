<?php

namespace App\Http\Resources;

use App\Models\GameMatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/** @mixin GameMatch */
#[OA\Schema(
    schema: 'MatchData',
    required: ['id', 'game_type_id', 'game_type', 'created_at', 'finished_at', 'duration', 'is_finished'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'game_type_id', type: 'integer', example: 1),
        new OA\Property(property: 'game_type', ref: '#/components/schemas/GameTypeData'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'finished_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'duration', type: 'integer', example: 465, nullable: true),
        new OA\Property(property: 'is_finished', type: 'boolean', example: false),
    ],
    type: 'object',
)]
class MatchResource extends JsonResource
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
            'game_type_id' => $this->game_type_id,
            'game_type' => $this->whenLoaded(
                'gameType',
                fn (): GameTypeResource => new GameTypeResource($this->gameType),
            ),
            'created_at' => $this->created_at->toISOString(),
            'finished_at' => $this->finished_at?->toISOString(),
            'duration' => $this->duration,
            'is_finished' => $this->finished_at !== null,
        ];
    }
}
