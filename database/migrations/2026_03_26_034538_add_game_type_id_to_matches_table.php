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
            $table->foreignId('game_type_id')
                ->nullable()
                ->after('id')
                ->comment('Referenced game type assigned to the match.');
        });

        $defaultGameTypeId = DB::table('game_types')
            ->orderBy('id')
            ->value('id');

        if ($defaultGameTypeId !== null) {
            DB::table('matches')
                ->whereNull('game_type_id')
                ->update(['game_type_id' => $defaultGameTypeId]);
        }

        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('game_type_id')
                ->nullable(false)
                ->change();

            $table->foreign('game_type_id')
                ->references('id')
                ->on('game_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['game_type_id']);
            $table->dropColumn('game_type_id');
        });
    }
};
