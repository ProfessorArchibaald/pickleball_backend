<?php

namespace Tests\Unit\Http\Requests\Api;

use App\Data\Matches\StoreMatchData;
use App\Http\Requests\Api\StoreMatchRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreMatchRequestTest extends TestCase
{
    public function test_request_can_return_validated_store_match_data(): void
    {
        $request = StoreMatchRequest::create('/api/matches', 'POST', [
            'game_type_id' => 123,
            'game_format_id' => 2,
            'players' => [
                ['user_id' => 11],
                ['user_id' => 12],
            ],
        ]);
        $request->setUserResolver(function (): User {
            $user = new User();
            $user->id = 77;

            return $user;
        });

        $validator = Validator::make($request->all(), [
            'game_type_id' => ['required', 'integer'],
            'game_format_id' => ['required', 'integer'],
            'players' => ['required', 'array'],
            'players.*.user_id' => ['required', 'integer'],
        ]);

        $this->assertFalse($validator->fails());

        $request->setValidator($validator);

        $data = $request->toData();

        $this->assertInstanceOf(StoreMatchData::class, $data);
        $this->assertSame(123, $data->gameTypeId);
        $this->assertSame(2, $data->gameFormatId);
        $this->assertSame([11, 12], $data->playerUserIds);
        $this->assertSame(77, $data->creatorUserId);
    }
}
