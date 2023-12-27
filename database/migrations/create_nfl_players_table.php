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
        Schema::create('nfl_players', function (Blueprint $table) {
            $table->id();
            $table->string('gsis_id');
            $table->string('status')->nullable();
            $table->string('display_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('short_name')->nullable();
            $table->string('current_team_id')->nullable();
            $table->string('espn_id')->nullable();
            $table->string('sportradar_id')->nullable();
            $table->string('yahoo_id')->nullable();
            $table->string('rotowire_id')->nullable();
            $table->string('pff_id')->nullable();
            $table->string('pfr_id')->nullable();
            $table->string('fantasy_data_id')->nullable();
            $table->string('sleeper_id')->nullable();
            $table->string('esb_id')->nullable();
            $table->string('gsis_it_id')->nullable();
            $table->string('smart_id')->nullable();
            $table->string('mfl_id')->nullable();
            $table->string('fantasypros_id')->nullable();
            $table->string('nfl_id')->nullable();
            $table->string('fleaflicker_id')->nullable();
            $table->string('cbs_id')->nullable();
            $table->string('rotoworld_id')->nullable();
            $table->string('ktc_id')->nullable();
            $table->string('stats_id')->nullable();
            $table->string('swish_id')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('college_name')->nullable();
            $table->string('college_conference')->nullable();
            $table->string('position_group')->nullable();
            $table->string('position')->nullable();
            $table->integer('jersey_number')->nullable();
            $table->integer('height')->nullable();
            $table->integer('weight')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->integer('entry_year')->nullable();
            $table->integer('rookie_year')->nullable();
            $table->string('draft_club')->nullable();
            $table->string('status_description_abbr')->nullable();
            $table->string('status_short_description')->nullable();
            $table->integer('draft_number')->nullable();
            $table->string('uniform_number')->nullable();
            $table->string('draft_round')->nullable();
            $table->string('headshot')->nullable();
            // Add more columns as needed
            $table->timestamps();
        });

        // Create table indexes
        Schema::table('nfl_players', function ($table) {
            $table->index('gsis_id', 'nfl_players_gsis_id_index');
            $table->index('current_team_id', 'nfl_players_current_team_id_index');
            $table->index('pfr_id', 'nfl_players_pfr_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfl_players');
    }
};
            