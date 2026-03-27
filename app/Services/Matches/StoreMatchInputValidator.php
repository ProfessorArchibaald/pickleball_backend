<?php

namespace App\Services\Matches;

use App\Models\Dictionary\Game\GameFormat;
use App\Models\Dictionary\Game\GameFormatType;
use App\Models\Dictionary\Game\GameType;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator as ValidatorContract;

class StoreMatchInputValidator
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(int $creatorUserId, string $playersField, string $playerUserIdField): array
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
                Rule::exists(GameFormat::class, 'id'),
            ],
            $playersField => [
                'bail',
                'required',
                'array',
            ],
            $playerUserIdField => [
                'bail',
                'required',
                'integer',
                'distinct',
                Rule::exists(User::class, 'id'),
                Rule::notIn([$creatorUserId]),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, callable(ValidatorContract): void>
     */
    public function after(array $data, string $playersField): array
    {
        return [
            function (ValidatorContract $validator) use ($data, $playersField): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $gameTypeId = (int) ($data['game_type_id'] ?? 0);
                $gameFormatId = (int) ($data['game_format_id'] ?? 0);

                $isAllowedForGameType = GameFormatType::query()
                    ->where('game_type_id', $gameTypeId)
                    ->where('game_format_id', $gameFormatId)
                    ->exists();

                if (! $isAllowedForGameType) {
                    $validator->errors()->add(
                        'game_format_id',
                        'The selected game format is invalid for the provided game type.',
                    );

                    return;
                }

                $gameFormat = GameFormat::query()
                    ->select(['id', 'number_of_players'])
                    ->find($gameFormatId);

                if ($gameFormat === null) {
                    return;
                }

                $expectedPlayersCount = $gameFormat->number_of_players - 1;
                $actualPlayersCount = count($data[$playersField] ?? []);

                if ($actualPlayersCount !== $expectedPlayersCount) {
                    $validator->errors()->add(
                        $playersField,
                        "The {$playersField} field must contain exactly {$expectedPlayersCount} players for the selected game format.",
                    );
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(string $playerUserIdField): array
    {
        return [
            "{$playerUserIdField}.not_in" => 'The authenticated user is added automatically and must not be included in players.',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function validate(
        array $data,
        int $creatorUserId,
        string $playersField,
        string $playerUserIdField,
    ): array {
        $validator = Validator::make(
            $data,
            $this->rules($creatorUserId, $playersField, $playerUserIdField),
            $this->messages($playerUserIdField),
        );

        foreach ($this->after($data, $playersField) as $afterCallback) {
            $validator->after($afterCallback);
        }

        return $validator->validate();
    }
}
