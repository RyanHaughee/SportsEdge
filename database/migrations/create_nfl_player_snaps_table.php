<?php

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
        Schema::create('nfl_player_snaps', function (Blueprint $table) {
            $table->id();
            $table->string('game_id');
            $table->string('pfr_game_id')->nullable();
            $table->integer('season');
            $table->string('game_type')->nullable();
            $table->integer('week');
            $table->string('player')->nullable();
            $table->string('pfr_player_id');
            $table->string('position')->nullable();
            $table->string('team')->nullable();
            $table->string('opponent')->nullable();
            $table->integer('offense_snaps')->nullable();
            $table->float('offense_pct')->nullable();
            $table->integer('defense_snaps')->nullable();
            $table->float('defense_pct')->nullable();
            $table->integer('st_snaps')->nullable();
            $table->float('st_pct')->nullable();
            // Add more columns as needed
            $table->timestamps();
        });

        // Create table indexes
        Schema::table('nfl_player_snaps', function ($table) {
            $table->index('pfr_player_id', 'nfl_player_snaps_pfr_player_id_index');
            $table->index('game_id', 'nfl_player_snaps_game_id_index');
            $table->index('pfr_game_id', 'nfl_player_snaps_pfr_game_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfl_player_snaps');
    }
};
