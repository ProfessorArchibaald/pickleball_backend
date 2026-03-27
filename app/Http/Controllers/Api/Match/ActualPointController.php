<?php

namespace App\Http\Controllers\Api\Match;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchPointResource;
use App\Models\GameMatch;
use App\Services\Matches\GetActualMatchPointService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ActualPointController extends Controller
{
    #[OA\Get(
        path: '/api/matches/{match}/actual-point',
        operationId: 'apiMatchesActualPoint',
        summary: 'Get the latest point of a match.',
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
                description: 'Latest match point returned successfully.',
                content: new OA\JsonContent(
                    required: ['data'],
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/MatchPointData'),
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
                description: 'Match or match point was not found.',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse'),
            ),
        ],
    )]
    public function __invoke(GameMatch $match, GetActualMatchPointService $getActualMatchPointService): JsonResponse
    {
        return MatchPointResource::make($getActualMatchPointService->get($match))->response();
    }
}
