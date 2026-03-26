<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedTinyInteger('game_format_id')
                ->nullable()
                ->after('game_type_id')
                ->comment('Referenced game format assigned to the match.');
        });

        $fallbackGameFormatId = DB::table('game_formats')
            ->orderBy('id')
            ->value('id');

        if ($fallbackGameFormatId === null) {
            throw new RuntimeException('At least one game format must exist before assigning match formats.');
        }

        $gameFormatIdsByGameTypeId = DB::table('game_format_types')
            ->pluck('game_format_id', 'game_type_id');

        DB::table('matches')
            ->orderBy('id')
            ->chunkById(100, function ($matches) use ($fallbackGameFormatId, $gameFormatIdsByGameTypeId): void {
                foreach ($matches as $match) {
                    DB::table('matches')
                        ->where('id', $match->id)
                        ->update([
                            'game_format_id' => $gameFormatIdsByGameTypeId[$match->game_type_id] ?? $fallbackGameFormatId,
                        ]);
                }
            });

        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedTinyInteger('game_format_id')
                ->nullable(false)
                ->change();

            $table->foreign('game_format_id')
                ->references('id')
                ->on('game_formats')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['game_format_id']);
            $table->dropColumn('game_format_id');
        });
    }
};
