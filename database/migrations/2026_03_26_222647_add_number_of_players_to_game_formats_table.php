<?php

use App\Models\Dictionary\Game\GameFormat;
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
        Schema::table('game_formats', static function (Blueprint $table) {
            $table->unsignedInteger('number_of_players')
                ->nullable()
                ->after('name')
                ->comment('Total number of players participating in the game format.');
        });

        collect(GameFormat::NUMBER_OF_PLAYERS)
            ->each(function (int $numberOfPlayers, int $gameFormatId): void {
                DB::table('game_formats')
                    ->where('id', $gameFormatId)
                    ->update(['number_of_players' => $numberOfPlayers]);
            });

        Schema::table('game_formats', static function (Blueprint $table) {
            $table->unsignedInteger('number_of_players')
                ->nullable(false)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_formats', static function (Blueprint $table) {
            $table->dropColumn('number_of_players');
        });
    }
};
