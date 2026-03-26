<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class LogoutController extends Controller
{
    #[OA\Post(
        path: '/api/auth/logout',
        operationId: 'apiAuthLogout',
        summary: 'Revoke the current Sanctum token.',
        security: [['sanctumBearer' => []]],
        tags: ['API Auth'],
        responses: [
            new OA\Response(response: 204, description: 'Current access token revoked.'),
            new OA\Response(
                response: 401,
                description: 'Authentication is required.',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse'),
            ),
        ],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(status: 204);
    }
}
