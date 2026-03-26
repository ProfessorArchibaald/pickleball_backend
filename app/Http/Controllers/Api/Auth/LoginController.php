<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class LoginController extends Controller
{
    #[OA\Post(
        path: '/api/auth/login',
        operationId: 'apiAuthLogin',
        summary: 'Log in and issue a Sanctum bearer token.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ApiLoginRequest'),
        ),
        tags: ['API Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Bearer token issued successfully.',
                content: new OA\JsonContent(
                    required: ['data'],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            required: ['token', 'token_type', 'user'],
                            properties: [
                                new OA\Property(property: 'token', type: 'string', example: '1|abcdefghijklmnopqrstuvwxyz'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                                new OA\Property(property: 'user', ref: '#/components/schemas/UserData'),
                            ],
                            type: 'object',
                        ),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failure or invalid credentials.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $email = Str::lower($request->validated('email'));

        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();

        if ($user === null || $user->isBlocked() || ! Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        $deviceName = $request->validated('device_name');

        $user->tokens()->where('name', $deviceName)->delete();

        $token = $user->createToken($deviceName);

        return response()->json([
            'data' => [
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'user' => UserResource::make($user),
            ],
        ]);
    }
}
