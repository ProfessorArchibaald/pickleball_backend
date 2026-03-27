<?php

namespace Tests\Feature\Database;

use App\Models\Dictionary\Game\GameFormat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefaultGameFormatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_game_formats_exist_after_migrations(): void
    {
        $gameFormats = GameFormat::query()
            ->orderBy('id')
            ->get(['id', 'name', 'number_of_players'])
            ->map(fn (GameFormat $gameFormat): array => [
                'id' => $gameFormat->id,
                'name' => $gameFormat->name,
                'number_of_players' => $gameFormat->number_of_players,
            ])
            ->all();

        $this->assertSame([
            ['id' => 1, 'name' => '1x1', 'number_of_players' => 2],
            ['id' => 2, 'name' => '2x2', 'number_of_players' => 4],
            ['id' => 3, 'name' => '1x2', 'number_of_players' => 3],
        ], $gameFormats);
    }

    public function test_game_formats_cannot_be_created_manually(): void
    {
        $exception = $this->captureValidationException(fn (): GameFormat => GameFormat::query()->create([
            'id' => 4,
            'name' => '2x1',
        ]));

        $this->assertSame(
            ['Game formats cannot be created manually.'],
            $exception->errors()['game_format'],
        );
    }

    public function test_game_format_number_of_players_can_be_updated(): void
    {
        $gameFormat = GameFormat::query()->findOrFail(1);

        $gameFormat->update([
            'number_of_players' => 5,
        ]);

        $this->assertSame(5, $gameFormat->fresh()->number_of_players);
    }

    public function test_only_number_of_players_can_be_updated_for_game_formats(): void
    {
        $gameFormat = GameFormat::query()->findOrFail(1);

        $exception = $this->captureValidationException(fn (): bool => $gameFormat->update([
            'name' => 'updated',
        ]));

        $this->assertSame(
            ['Only the number of players can be updated for game formats.'],
            $exception->errors()['game_format'],
        );
    }

    public function test_game_formats_cannot_be_deleted(): void
    {
        $gameFormat = GameFormat::query()->findOrFail(1);

        $exception = $this->captureValidationException(fn (): ?bool => $gameFormat->delete());

        $this->assertSame(
            ['Game formats cannot be deleted.'],
            $exception->errors()['game_format'],
        );
    }
}
