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
        Schema::create('nfl_def_adv_stats', function (Blueprint $table) {
            $table->id();

            $table->string('game_id');
            $table->string('pfr_game_id');
            $table->integer('season');
            $table->integer('week');
            $table->string('game_type')->nullable();
            $table->string('team')->nullable();
            $table->string('opponent')->nullable();
            $table->string('pfr_player_name')->nullable();
            $table->string('pfr_player_id');
            $table->integer('def_ints')->nullable();
            $table->integer('def_targets')->nullable();
            $table->integer('def_completions_allowed')->nullable();
            $table->float('def_completion_pct')->nullable();
            $table->integer('def_yards_allowed')->nullable();
            $table->float('def_yards_allowed_per_cmp')->nullable();
            $table->float('def_yards_allowed_per_tgt')->nullable();
            $table->integer('def_receiving_td_allowed')->nullable();
            $table->float('def_passer_rating_allowed')->nullable();
            $table->float('def_adot')->nullable();
            $table->integer('def_air_yards_completed')->nullable();
            $table->integer('def_yards_after_catch')->nullable();
            $table->integer('def_times_blitzed')->nullable();
            $table->integer('def_times_hurried')->nullable();
            $table->integer('def_times_hitqb')->nullable();
            $table->float('def_sacks')->nullable();
            $table->integer('def_pressures')->nullable();
            $table->float('def_tackles_combined')->nullable();
            $table->integer('def_missed_tackles')->nullable();
            $table->float('def_missed_tackle_pct')->nullable();
            // Add more columns as needed
            $table->timestamps();
        });

        // Create table indexes
        Schema::table('nfl_def_adv_stats', function ($table) {
            $table->index('pfr_player_id', 'nfl_def_adv_stats_pfr_player_id_index');
            $table->index('game_id', 'nfl_def_adv_stats_game_id_index');
            $table->index('pfr_game_id', 'nfl_def_adv_stats_pfr_game_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfl_def_adv_stats');
    }
};

