<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('match_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')
                ->constrained('matches')
                ->cascadeOnUpdate()
                ->cascadeOnDelete()
                ->comment('Referenced match that the player participates in.');
            $table->foreignIdFor(User::class)
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete()
                ->comment('Referenced user participating in the match.');
            $table->unsignedTinyInteger('team')
                ->comment('Team number assigned to the player within the match. Expected values are 1 or 2.');
            $table->boolean('is_creator')
                ->default(false)
                ->comment('Indicates whether the player created the match.');
            $table->timestamps();
            $table->index('match_id');
            $table->index('user_id');
            $table->comment('Stores players participating in a match.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_players');
    }
};
