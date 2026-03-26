<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/** @mixin User */
#[OA\Schema(
    schema: 'UserData',
    required: ['id', 'name', 'email', 'is_blocked'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Test'),
        new OA\Property(property: 'last_name', type: 'string', example: 'User', nullable: true),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@example.com'),
        new OA\Property(property: 'is_blocked', type: 'boolean', example: false),
        new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object',
)]
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'is_blocked' => $this->is_blocked,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
        ];
    }
}
