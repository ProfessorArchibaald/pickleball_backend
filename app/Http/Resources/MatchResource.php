<?php

namespace App\Http\Resources;

use App\Models\GameMatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin GameMatch */
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
