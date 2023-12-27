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
        Schema::create('nfl_off_stats', function (Blueprint $table) {
             $table->id();
             $table->string('player_id');
             $table->string('player_name')->nullable();
             $table->string('player_display_name')->nullable();
             $table->string('position')->nullable();
             $table->string('position_group')->nullable();
             $table->string('headshot_url')->nullable();
             $table->string('recent_team')->nullable();
             $table->integer('season');
             $table->integer('week');
             $table->string('season_type')->nullable();
             $table->integer('completions')->nullable();
             $table->integer('attempts')->nullable();
             $table->integer('passing_yards')->nullable();
             $table->integer('passing_tds')->nullable();
             $table->integer('interceptions')->nullable();
             $table->integer('sacks')->nullable();
             $table->integer('sack_yards')->nullable();
             $table->integer('sack_fumbles')->nullable();
             $table->integer('sack_fumbles_lost')->nullable();
             $table->integer('passing_air_yards')->nullable();
             $table->integer('passing_yards_after_catch')->nullable();
             $table->integer('passing_first_downs')->nullable();
             $table->float('passing_epa')->nullable();
             $table->integer('passing_2pt_conversions')->nullable();
             $table->float('pacr')->nullable();
             $table->float('dakota')->nullable();
             $table->integer('carries')->nullable();
             $table->integer('rushing_yards')->nullable();
             $table->integer('rushing_tds')->nullable();
             $table->integer('rushing_fumbles')->nullable();
             $table->integer('rushing_fumbles_lost')->nullable();
             $table->integer('rushing_first_downs')->nullable();
             $table->float('rushing_epa')->nullable();
             $table->integer('rushing_2pt_conversions')->nullable();
             $table->integer('receptions')->nullable();
             $table->integer('targets')->nullable();
             $table->integer('receiving_yards')->nullable();
             $table->integer('receiving_tds')->nullable();
             $table->integer('receiving_fumbles')->nullable();
             $table->integer('receiving_fumbles_lost')->nullable();
             $table->integer('receiving_air_yards')->nullable();
             $table->integer('receiving_yards_after_catch')->nullable();
             $table->integer('receiving_first_downs')->nullable();
             $table->float('receiving_epa')->nullable();
             $table->integer('receiving_2pt_conversions')->nullable();
             $table->float('racr')->nullable();
             $table->float('target_share')->nullable();
             $table->float('air_yards_share')->nullable();
             $table->float('wopr')->nullable();
             $table->integer('special_teams_tds')->nullable();
             $table->float('fantasy_points')->nullable();
             $table->float('fantasy_points_ppr')->nullable();
             $table->string('opponent_team')->nullable();
             // Add more columns as needed
             $table->timestamps();
        });

        // Create table indexes
        Schema::table('nfl_off_stats', function ($table) {
            $table->index('player_id', 'nfl_off_stats_player_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfl_off_stats');
    }
};
            