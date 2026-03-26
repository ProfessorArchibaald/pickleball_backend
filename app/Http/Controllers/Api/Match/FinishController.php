<?php

namespace App\Http\Controllers\Api\Match;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchResource;
use App\Models\GameMatch;
use App\Services\Matches\FinishMatchService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class FinishController extends Controller
{
    #[OA\Patch(
        path: '/api/matches/{match}/finish',
        operationId: 'apiMatchesFinish',
        summary: 'Mark a match as finished.',
        security: [['sanctumBearer' => []]],
        tags: ['API Match'],
        parameters: [
            new OA\Parameter(
                name: 'match',
                description: 'Match identifier.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Match finished successfully.',
                content: new OA\JsonContent(
                    required: ['data'],
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/MatchData'),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication is required.',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse'),
            ),
            new OA\Response(
                response: 404,
                description: 'Match was not found.',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse'),
            ),
        ],
    )]
    public function __invoke(GameMatch $match, FinishMatchService $finishMatchService): JsonResponse
    {
        return MatchResource::make($finishMatchService->finish($match))->response();
    }
}
