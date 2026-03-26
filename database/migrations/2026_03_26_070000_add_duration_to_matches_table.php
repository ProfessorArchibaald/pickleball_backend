<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table): void {
            $table->unsignedInteger('duration')
                ->nullable()
                ->after('finished_at')
                ->comment('Match duration in seconds.');
        });

        DB::table('matches')
            ->whereNotNull('finished_at')
            ->update([
                'duration' => DB::raw('TIMESTAMPDIFF(SECOND, created_at, finished_at)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table): void {
            $table->dropColumn('duration');
        });
    }
};
