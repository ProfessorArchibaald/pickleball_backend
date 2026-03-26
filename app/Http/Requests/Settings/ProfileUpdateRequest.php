<?php

namespace App\Http\Requests\Settings;

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SettingsProfileUpdateRequest',
    required: ['name', 'last_name', 'email'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Test', maxLength: 255),
        new OA\Property(property: 'last_name', type: 'string', example: 'User', maxLength: 255),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@example.com', maxLength: 255),
    ],
    type: 'object',
)]
class ProfileUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->profileRules($this->user()->id);
    }
}
