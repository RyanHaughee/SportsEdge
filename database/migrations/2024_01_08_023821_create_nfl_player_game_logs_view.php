<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNFLPlayerGameLogsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        CREATE OR REPLACE VIEW _nfl_player_game_logs AS
        
        SELECT
            os.player_id,
            p.display_name AS player_display_name,
            os.position,
            os.position_group,
            os.headshot_url,
            t.team_id,
            t.team_abbr,
            os.season,
            os.week,
            os.season_type,
            os.completions,
            os.attempts,
            os.passing_yards,
            os.passing_tds,
            os.interceptions,
            os.sacks,
            os.sack_yards,
            os.sack_fumbles,
            os.sack_fumbles_lost,
            os.passing_air_yards,
            os.passing_yards_after_catch,
            os.passing_first_downs,
            os.passing_epa,
            os.passing_2pt_conversions,
            os.pacr,
            os.dakota,
            os.carries,
            os.rushing_yards,
            os.rushing_tds,
            os.rushing_fumbles,
            os.rushing_fumbles_lost,
            os.rushing_first_downs,
            os.rushing_epa,
            os.rushing_2pt_conversions,
            os.receptions,
            os.targets,
            os.receiving_yards,
            os.receiving_tds,
            os.receiving_fumbles,
            os.receiving_fumbles_lost,
            os.receiving_air_yards,
            os.receiving_yards_after_catch,
            os.receiving_first_downs,
            os.receiving_epa,
            os.receiving_2pt_conversions,
            os.racr,
            os.target_share,
            os.air_yards_share,
            os.wopr,
            os.special_teams_tds,
            os.fantasy_points,
            os.fantasy_points_ppr,
            os.opponent_team,
            oas.game_id,
            oas.passing_drops,
            oas.passing_drop_pct,
            oas.receiving_drop,
            oas.receiving_drop_pct,
            oas.passing_bad_throws,
            oas.passing_bad_throw_pct,
            oas.times_sacked,
            oas.times_blitzed,
            oas.times_hurried,
            oas.times_hit,
            oas.times_pressured,
            oas.times_pressured_pct,
            oas.rushing_yards_before_contact,
            oas.rushing_yards_before_contact_avg,
            oas.rushing_yards_after_contact,
            oas.rushing_yards_after_contact_avg,
            oas.rushing_broken_tackles,
            oas.receiving_broken_tackles,
            oas.receiving_int,
            oas.receiving_rat,
            ongs.avg_time_to_throw,
            ongs.avg_completed_air_yards,
            ongs.avg_intended_air_yards,
            ongs.avg_air_yards_differential,
            ongs.aggressiveness,
            ongs.max_completed_air_distance,
            ongs.avg_air_yards_to_sticks,
            ongs.passer_rating,
            ongs.completion_percentage,
            ongs.expected_completion_percentage,
            ongs.completion_percentage_above_expectation,
            ongs.avg_air_distance,
            ongs.max_air_distance,
            ongs.avg_cushion,
            ongs.avg_separation,
            ongs.avg_targeted_air_yards,
            ongs.percent_share_of_targeted_air_yards,
            ongs.catch_percentage,
            ongs.avg_yac,
            ongs.avg_expected_yac,
            ongs.avg_yac_above_expectation,
            ongs.efficiency,
            ongs.percent_attempts_gte_eight_defenders,
            ongs.avg_time_to_los,
            ongs.expected_rush_yards,
            ongs.rush_yards_over_expected,
            ongs.avg_rush_yards,
            ongs.rush_yards_over_expected_per_att,
            ongs.rush_pct_over_expected
        FROM nfl_off_stats os
        JOIN nfl_players p
            ON p.gsis_id = os.player_id
        JOIN nfl_teams t
            ON t.team_abbr = os.recent_team
        LEFT JOIN nfl_off_adv_stats oas
                ON oas.pfr_player_id = p.pfr_id
                AND oas.season = os.season
                AND oas.week = os.week
        LEFT JOIN nfl_off_nextgen_stats ongs
            ON ongs.player_gsis_id = os.player_id
             AND ongs.season = os.season
             AND ongs.week = os.week
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW _nfl_player_game_logs");
    }
}

