<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ApiLoginRequest',
    required: ['email', 'password', 'device_name'],
    properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'player@example.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
        new OA\Property(property: 'device_name', type: 'string', example: 'iphone-16', maxLength: 255),
    ],
    type: 'object',
)]
class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:255'],
        ];
    }
}
