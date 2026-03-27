<?php

namespace App\Http\Requests\Api;

use App\Data\Matches\StoreMatchData;
use App\Services\Matches\StoreMatchDataFactory;
use App\Services\Matches\StoreMatchInputValidator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ApiStoreMatchRequest',
    required: ['game_type_id', 'game_format_id', 'players'],
    properties: [
        new OA\Property(property: 'game_type_id', type: 'integer', example: 1),
        new OA\Property(property: 'game_format_id', type: 'integer', example: 2),
        new OA\Property(
            property: 'players',
            type: 'array',
            items: new OA\Items(
                required: ['user_id'],
                properties: [
                    new OA\Property(property: 'user_id', type: 'integer', example: 5),
                ],
                type: 'object',
            ),
        ),
    ],
    type: 'object',
)]
class StoreMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return app(StoreMatchInputValidator::class)->rules(
            creatorUserId: (int) $this->user()->getKey(),
            playersField: 'players',
            playerUserIdField: 'players.*.user_id',
        );
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return app(StoreMatchInputValidator::class)->after(
            data: $this->all(),
            playersField: 'players',
        );
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return app(StoreMatchInputValidator::class)->messages('players.*.user_id');
    }

    public function toData(): StoreMatchData
    {
        return app(StoreMatchDataFactory::class)->fromApiPayload(
            validatedData: $this->validated(),
            creatorUserId: (int) $this->user()->getKey(),
        );
    }
}
