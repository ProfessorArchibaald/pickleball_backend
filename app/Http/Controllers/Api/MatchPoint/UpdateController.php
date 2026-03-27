<?php

namespace App\Http\Controllers\Api\MatchPoint;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateMatchPointRequest;
use App\Http\Resources\MatchPointResource;
use App\Models\MatchPoint;
use App\Services\Matches\MatchActionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class UpdateController extends Controller
{
    #[OA\Patch(
        path: '/api/match-points/{matchPoint}',
        operationId: 'apiMatchPointsUpdate',
        summary: 'Update the winning player of a match point.',
        security: [['sanctumBearer' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ApiUpdateMatchPointRequest'),
        ),
        tags: ['API Match'],
        parameters: [
            new OA\Parameter(
                name: 'matchPoint',
                description: 'Match point identifier.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Match point updated successfully.',
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
                description: 'Match point was not found.',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse'),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failure.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function __invoke(
        UpdateMatchPointRequest $request,
        MatchPoint $matchPoint,
        MatchActionService $matchActionService,
    ): JsonResponse {
        $actualMatchPoint = DB::transaction(function () use ($request, $matchPoint, $matchActionService) {
            $matchPoint->update($request->validated());

            return $matchActionService->handle($matchPoint->gameMatch);
        });

        return MatchPointResource::make($actualMatchPoint)->response()->setStatusCode(200);
    }
}
