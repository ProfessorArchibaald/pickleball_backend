<?php

use App\Models\MatchPlayer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('match_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')
                ->constrained('matches')
                ->cascadeOnUpdate()
                ->cascadeOnDelete()
                ->comment('Referenced match that the point belongs to.');
            $table->foreignIdFor(MatchPlayer::class, 'serve_player_id')
                ->constrained('match_players')
                ->cascadeOnUpdate()
                ->cascadeOnDelete()
                ->comment('Referenced match player who served the point.');
            $table->unsignedInteger('team1_score')
                ->comment('Current score of team 1 after this point.');
            $table->unsignedInteger('team2_score')
                ->comment('Current score of team 2 after this point.');
            $table->foreignIdFor(MatchPlayer::class, 'win_point_player_id')
                ->nullable()
                ->constrained('match_players')
                ->cascadeOnUpdate()
                ->cascadeOnDelete()
                ->comment('Referenced match player who won the point.');
            $table->timestamps();
            $table->index('match_id');
            $table->index('serve_player_id');
            $table->index('win_point_player_id');
            $table->comment('Stores each scored point within a match.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_points');
    }
};
