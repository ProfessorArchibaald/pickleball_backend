<?php

namespace App\Http\Requests\Api;

use App\Models\MatchPlayer;
use App\Models\MatchPoint;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ApiUpdateMatchPointRequest',
    required: ['win_point_player_id'],
    properties: [
        new OA\Property(property: 'win_point_player_id', type: 'integer', example: 12),
    ],
    type: 'object',
)]
class UpdateMatchPointRequest extends FormRequest
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
        /** @var MatchPoint $matchPoint */
        $matchPoint = $this->route('matchPoint');

        return [
            'win_point_player_id' => [
                'required',
                'integer',
                Rule::exists(MatchPlayer::class, 'id')
                    ->where('match_id', $matchPoint->match_id),
            ],
        ];
    }
}
