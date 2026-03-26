<?php

namespace App\Http\Requests\Api;

use App\Data\Matches\StoreMatchData;
use App\Models\Dictionary\GameType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
                'required',
                'integer',
                Rule::exists(GameType::class, 'id'),
            ],
        ];
    }

    public function toData(): StoreMatchData
    {
        return StoreMatchData::from($this->safe()->all());
    }
}
