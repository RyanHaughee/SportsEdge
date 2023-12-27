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
        Schema::create('nfl_off_adv_stats', function (Blueprint $table) {
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
             $table->integer('passing_drops')->nullable();
             $table->float('passing_drop_pct')->nullable();
             $table->integer('receiving_drop')->nullable();
             $table->float('receiving_drop_pct')->nullable();
             $table->integer('passing_bad_throws')->nullable();
             $table->float('passing_bad_throw_pct')->nullable();
             $table->integer('times_sacked')->nullable();
             $table->integer('times_blitzed')->nullable();
             $table->integer('times_hurried')->nullable();
             $table->integer('times_hit')->nullable();
             $table->integer('times_pressured')->nullable();
             $table->float('times_pressured_pct')->nullable();
             $table->integer('carries')->nullable();
             $table->integer('rushing_yards_before_contact')->nullable();
             $table->float('rushing_yards_before_contact_avg')->nullable();
             $table->integer('rushing_yards_after_contact')->nullable();
             $table->float('rushing_yards_after_contact_avg')->nullable();
             $table->integer('rushing_broken_tackles')->nullable();
             $table->integer('receiving_broken_tackles')->nullable();
             $table->integer('receiving_int')->nullable();
             $table->float('receiving_rat')->nullable();
            // Add more columns as needed
            $table->timestamps();
        });

        // Create table indexes
        Schema::table('nfl_off_adv_stats', function ($table) {
            $table->index('pfr_player_id', 'nfl_off_adv_stats_pfr_player_id_index');
            $table->index('game_id', 'nfl_off_adv_stats_game_id_index');
            $table->index('pfr_game_id', 'nfl_off_adv_stats_pfr_game_id_index');
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

             