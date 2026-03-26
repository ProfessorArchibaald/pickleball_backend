<?php

namespace Tests\Feature\Database;

use App\Models\Dictionary\Game\GameType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefaultGameTypesTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_game_type_exists_after_migrations(): void
    {
        $gameTypes = GameType::query()->orderBy('id')->pluck('name')->all();

        $this->assertCount(1, $gameTypes);
        $this->assertSame([GameType::DEFAULT_NAME], $gameTypes);
    }

    public function test_second_game_type_cannot_be_created(): void
    {
        $exception = $this->captureValidationException(fn (): GameType => GameType::query()->create([
            'name' => 'Another game type',
        ]));

        $this->assertSame(
            ['Only one game type can exist.'],
            $exception->errors()['game_type'],
        );
    }

    public function test_game_type_cannot_be_deleted(): void
    {
        $gameType = GameType::query()->firstOrFail();

        $exception = $this->captureValidationException(fn (): ?bool => $gameType->delete());

        $this->assertSame(
            ['Game types cannot be deleted.'],
            $exception->errors()['game_type'],
        );
    }
}
