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
        Schema::create('nfl_schedule', function (Blueprint $table) {
            $table->id();
            $table->string('game_id');
            $table->integer('season');
            $table->string('game_type')->nullable();
            $table->integer('week');
            $table->date('gameday');
            $table->string('weekday')->nullable();
            $table->time('gametime')->nullable();
            $table->string('away_team')->nullable();
            $table->integer('away_score')->nullable();
            $table->string('home_team')->nullable();
            $table->integer('home_score')->nullable();
            $table->string('location')->nullable();
            $table->integer('result')->nullable();
            $table->integer('total')->nullable();
            $table->integer('overtime')->nullable();
            $table->string('old_game_id')->nullable();
            $table->string('gsis')->nullable();
            $table->string('nfl_detail_id')->nullable();
            $table->string('pfr')->nullable();
            $table->string('pff')->nullable();
            $table->string('espn')->nullable();
            $table->string('ftn')->nullable();
            $table->integer('away_rest')->nullable();
            $table->integer('home_rest')->nullable();
            $table->integer('away_moneyline')->nullable();
            $table->integer('home_moneyline')->nullable();
            $table->float('spread_line')->nullable();
            $table->integer('away_spread_odds')->nullable();
            $table->integer('home_spread_odds')->nullable();
            $table->float('total_line')->nullable();
            $table->integer('under_odds')->nullable();
            $table->integer('over_odds')->nullable();
            $table->integer('div_game')->nullable();
            $table->string('roof')->nullable();
            $table->string('surface')->nullable();
            $table->integer('temp')->nullable();
            $table->integer('wind')->nullable();
            $table->string('away_qb_id')->nullable();
            $table->string('home_qb_id')->nullable();
            $table->string('away_qb_name')->nullable();
            $table->string('home_qb_name')->nullable();
            $table->string('away_coach')->nullable();
            $table->string('home_coach')->nullable();
            $table->string('referee')->nullable();
            $table->string('stadium_id')->nullable();
            $table->string('stadium')->nullable();
            // Add more columns as needed
            $table->timestamps();
        });

        // Create table indexes
        Schema::table('nfl_schedule', function ($table) {
            $table->index('game_id', 'nfl_schedule_game_id_index');
            $table->index('away_qb_id', 'nfl_schedule_away_qb_id_index');
            $table->index('home_qb_id', 'nfl_schedule_home_qb_id_index');
            $table->index('stadium_id', 'nfl_schedule_stadium_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfl_schedule');
    }
};
