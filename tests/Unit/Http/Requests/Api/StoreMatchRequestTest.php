<?php

namespace Tests\Unit\Http\Requests\Api;

use App\Data\Matches\StoreMatchData;
use App\Http\Requests\Api\StoreMatchRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreMatchRequestTest extends TestCase
{
    public function test_request_can_return_validated_store_match_data(): void
    {
        $request = StoreMatchRequest::create('/api/matches', 'POST', [
            'game_type_id' => 123,
        ]);

        $validator = Validator::make($request->all(), [
            'game_type_id' => ['required', 'integer'],
        ]);

        $this->assertFalse($validator->fails());

        $request->setValidator($validator);

        $data = $request->toData();

        $this->assertInstanceOf(StoreMatchData::class, $data);
        $this->assertSame(123, $data->gameTypeId);
    }
}
