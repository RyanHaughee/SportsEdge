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
        Schema::create('nfl_teams', function (Blueprint $table) {
            $table->id();
            $table->string('team_abbr');
            $table->string('team_name')->nullable();
            $table->string('team_id');
            $table->string('team_nick')->nullable();
            $table->string('team_conf')->nullable();
            $table->string('team_division')->nullable();
            $table->string('team_color')->nullable();
            $table->string('team_color2')->nullable();
            $table->string('team_color3')->nullable();
            $table->string('team_color4')->nullable();
            $table->string('team_logo_wikipedia')->nullable();
            $table->string('team_logo_espn')->nullable();
            $table->string('team_wordmark')->nullable();
            $table->string('team_conference_logo')->nullable();
            $table->string('team_league_logo')->nullable();
            $table->string('team_logo_squared')->nullable();
            // Add more columns as needed
            $table->timestamps();
        });

        // Create table indexes
        Schema::table('nfl_teams', function ($table) {
            $table->index('team_abbr', 'nfl_teams_team_abbr_index');
            $table->index('team_id', 'nfl_teams_team_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfl_teams');
    }
};
