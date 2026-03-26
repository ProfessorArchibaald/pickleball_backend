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
            ->get(['id', 'name'])
            ->map(fn (GameFormat $gameFormat): array => [
                'id' => $gameFormat->id,
                'name' => $gameFormat->name,
            ])
            ->all();

        $this->assertSame([
            ['id' => 1, 'name' => '1x1'],
            ['id' => 2, 'name' => '2x2'],
            ['id' => 3, 'name' => '1x2'],
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

    public function test_game_formats_cannot_be_updated(): void
    {
        $gameFormat = GameFormat::query()->findOrFail(1);

        $exception = $this->captureValidationException(fn (): bool => $gameFormat->update([
            'name' => 'updated',
        ]));

        $this->assertSame(
            ['Game formats cannot be updated.'],
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
