<?php

use App\Models\Dictionary\Game\GameFormat;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_formats', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('name')->unique()->comment('Display name of the fixed game format dictionary value.');
            $table->timestamps();
            $table->comment('Stores immutable built-in game format dictionary entries.');
        });

        $timestamp = now();

        DB::table('game_formats')->insert(
            collect(GameFormat::FORMATS)
                ->map(
                    fn (string $name, int $id): array => [
                        'id' => $id,
                        'name' => $name,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ],
                )
                ->values()
                ->all(),
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('game_formats');
    }
};
