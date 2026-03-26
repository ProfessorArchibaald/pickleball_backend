<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CurrentUserController extends Controller
{
    #[OA\Get(
        path: '/api/auth/user',
        operationId: 'apiAuthCurrentUser',
        summary: 'Return the authenticated API user.',
        security: [['sanctumBearer' => []]],
        tags: ['API User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authenticated user returned.',
                content: new OA\JsonContent(
                    required: ['data'],
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/UserData'),
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
    public function __invoke(Request $request): UserResource
    {
        return UserResource::make($request->user());
    }
}
