<?php

namespace App\Http\Controllers\Api\GameType;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameTypeResource;
use App\Models\Dictionary\Game\GameType;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class IndexController extends Controller
{
    #[OA\Get(
        path: '/api/game-types',
        operationId: 'apiGameTypesIndex',
        summary: 'Return the game type dictionary.',
        tags: ['API Game Type'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Game type dictionary returned.',
                content: new OA\JsonContent(
                    required: ['data'],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            items: new OA\Items(ref: '#/components/schemas/GameTypeData'),
                            type: 'array',
                        ),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function __invoke(): AnonymousResourceCollection
    {
        return GameTypeResource::collection(
            GameType::query()
                ->orderBy('id')
                ->get(),
        );
    }
}
