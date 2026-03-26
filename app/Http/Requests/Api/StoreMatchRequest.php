<?php

namespace App\Http\Requests\Api;

use App\Data\Matches\StoreMatchData;
use App\Models\Dictionary\Game\GameFormatType;
use App\Models\Dictionary\Game\GameType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ApiStoreMatchRequest',
    required: ['game_type_id', 'game_format_id'],
    properties: [
        new OA\Property(property: 'game_type_id', type: 'integer', example: 1),
        new OA\Property(property: 'game_format_id', type: 'integer', example: 2),
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
        return [
            'game_type_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists(GameType::class, 'id'),
            ],
            'game_format_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists(GameFormatType::class, 'game_format_id')
                    ->where('game_type_id', $this->integer('game_type_id')),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'game_format_id.exists' => 'The selected game format is invalid for the provided game type.',
        ];
    }

    public function toData(): StoreMatchData
    {
        return StoreMatchData::from($this->safe()->all());
    }
}
