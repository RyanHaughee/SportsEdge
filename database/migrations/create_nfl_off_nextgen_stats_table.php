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
        Schema::create('nfl_off_nextgen_stats', function (Blueprint $table) {
             $table->id();
             $table->integer('season');
             $table->string('season_type')->nullable();
             $table->integer('week');
             $table->string('player_display_name')->nullable();
             $table->string('player_position')->nullable();
             $table->string('team_abbr')->nullable();
             $table->float('avg_time_to_throw')->nullable();
             $table->float('avg_completed_air_yards')->nullable();
             $table->float('avg_intended_air_yards')->nullable();
             $table->float('avg_air_yards_differential')->nullable();
             $table->float('aggressiveness')->nullable();
             $table->float('max_completed_air_distance')->nullable();
             $table->float('avg_air_yards_to_sticks')->nullable();
             $table->integer('attempts')->nullable();
             $table->integer('pass_yards')->nullable();
             $table->integer('pass_touchdowns')->nullable();
             $table->integer('interceptions')->nullable();
             $table->float('passer_rating')->nullable();
             $table->integer('completions')->nullable();
             $table->float('completion_percentage')->nullable();
             $table->float('expected_completion_percentage')->nullable();
             $table->float('completion_percentage_above_expectation')->nullable();
             $table->float('avg_air_distance')->nullable();
             $table->float('max_air_distance')->nullable();
             $table->string('player_gsis_id');
             $table->string('player_first_name')->nullable();
             $table->string('player_last_name')->nullable();
             $table->integer('player_jersey_number')->nullable();
             $table->string('player_short_name')->nullable();
             // Add more columns as needed
             $table->timestamps();
        });

        // Create table indexes
        Schema::table('nfl_off_nextgen_stats', function ($table) {
            $table->index('player_gsis_id', 'nfl_off_nextgen_stats_player_gsis_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfl_off_nextgen_stats');
    }
};