<?php

namespace App\Http\Controllers\Api\GameType;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameFormatResource;
use App\Models\Dictionary\Game\GameFormat;
use App\Models\Dictionary\Game\GameType;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class FormatsIndexController extends Controller
{
    #[OA\Get(
        path: '/api/game-types/{gameType}/formats',
        operationId: 'apiGameTypesFormatsIndex',
        summary: 'Return the game formats available for a given game type.',
        tags: ['API Game Type'],
        parameters: [
            new OA\Parameter(
                name: 'gameType',
                description: 'Game type identifier.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Available game formats returned.',
                content: new OA\JsonContent(
                    required: ['data'],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/GameFormatData'),
                        ),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Game type was not found.',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse'),
            ),
        ],
    )]
    public function __invoke(GameType $gameType): AnonymousResourceCollection
    {
        return GameFormatResource::collection(
            GameFormat::query()
                ->whereHas('gameFormatTypes', fn ($query) => $query->where('game_type_id', $gameType->getKey()))
                ->orderBy('id')
                ->get(),
        );
    }
}
