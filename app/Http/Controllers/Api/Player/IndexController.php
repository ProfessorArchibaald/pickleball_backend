<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PlayerListResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class IndexController extends Controller
{
    #[OA\Get(
        path: '/api/players',
        operationId: 'apiPlayersIndex',
        summary: 'Return all existing players except the authenticated user.',
        security: [['sanctumBearer' => []]],
        tags: ['API Player'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Players returned successfully.',
                content: new OA\JsonContent(
                    required: ['data'],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            items: new OA\Items(ref: '#/components/schemas/PlayerListItemData'),
                            type: 'array',
                        ),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication is required.',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse'),
            ),
        ],
    )]
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        return PlayerListResource::collection(
            User::query()
                ->select(['id', 'name', 'last_name'])
                ->whereKeyNot($request->user()->getKey())
                ->orderBy('id')
                ->get(),
        );
    }
}
