<?php

namespace App\Http\Resources;

use App\Models\Dictionary\Game\GameType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/** @mixin GameType */
#[OA\Schema(
    schema: 'GameTypeData',
    required: ['id', 'name'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Pickleball'),
    ],
    type: 'object',
)]
class GameTypeResource extends JsonResource
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
            'name' => $this->name,
        ];
    }
}
