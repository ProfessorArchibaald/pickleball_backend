<?php

namespace App\Http\Resources;

use App\Models\Dictionary\Game\GameFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/** @mixin GameFormat */
#[OA\Schema(
    schema: 'GameFormatData',
    required: ['id', 'name', 'number_of_players'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'name', type: 'string', example: '2x2'),
        new OA\Property(property: 'number_of_players', type: 'integer', example: 4),
    ],
    type: 'object',
)]
class GameFormatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'number_of_players' => $this->number_of_players,
        ];
    }
}
