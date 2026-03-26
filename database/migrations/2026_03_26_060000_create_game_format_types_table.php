<?php

use App\Models\Dictionary\Game\GameType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_format_types', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(GameType::class)
                ->constrained('game_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->unsignedTinyInteger('game_format_id');
            $table->timestamps();
            $table->unique('game_type_id');
            $table->foreign('game_format_id')
                ->references('id')
                ->on('game_formats')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->comment('Stores the selected game format for each game type.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_format_types');
    }
};
