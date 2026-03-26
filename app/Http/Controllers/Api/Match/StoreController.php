<?php

namespace App\Http\Controllers\Api\Match;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMatchRequest;
use App\Http\Resources\MatchResource;
use App\Services\Matches\CreateMatchService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends Controller
{
    #[OA\Post(
        path: '/api/matches',
        operationId: 'apiMatchesStore',
        summary: 'Create a new match.',
        security: [['sanctumBearer' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ApiStoreMatchRequest'),
        ),
        tags: ['API Match'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Match created successfully.',
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
                response: 422,
                description: 'Validation failure.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function __invoke(StoreMatchRequest $request, CreateMatchService $createMatchService): JsonResponse
    {
        $match = $createMatchService->create($request->toData());

        return MatchResource::make($match)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
