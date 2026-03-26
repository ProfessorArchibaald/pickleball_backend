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

class LoginController extends Controller
{
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
