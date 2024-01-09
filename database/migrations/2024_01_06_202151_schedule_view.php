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
        //
        DB::statement("
        CREATE OR REPLACE VIEW _nfl_schedule AS
        (
            WITH _pivot AS (
            SELECT
                game_id,
                season,
                game_type,
                week,
                gameday,
                weekday,
                gametime,
                home_team AS team,
                case when location = 'Neutral' THEN 'Neutral' ELSE 'Home' END AS location,
                away_team AS opponent,
                home_score AS team_score,
                away_score AS opponent_score,
                result AS result,
                total,
                overtime,
                old_game_id,
                gsis,
                nfl_detail_id,
                pfr,
                pff,
                espn,
                ftn,
                home_rest AS team_rest,
                away_rest AS opponent_rest,
                home_moneyline AS team_moneyline,
                away_moneyline AS opponent_moneyline,
                spread_line AS spread_line,
                home_spread_odds AS team_spread_odds,
                away_spread_odds AS opponent_spread_odds,
                total_line,
                under_odds,
                over_odds,
                div_game,
                roof,
                surface,
                wind,
                home_qb_id AS team_qb_id,
                home_qb_name AS team_qb_name,
                away_qb_id AS opponent_qb_id,
                away_qb_name AS opponent_qb_name,
                home_coach AS team_coach,
                away_coach AS opponent_coach,
                referee,
                stadium_id,
                stadium
            FROM
                nfl_schedule
            
            UNION ALL
            
            SELECT
                game_id,
                season,
                game_type,
                week,
                gameday,
                weekday,
                gametime,
                away_team AS team,
                case when location = 'Neutral' THEN 'Neutral' ELSE 'Away' END AS location,
                home_team AS opponent,
                away_score AS team_score,
                home_score AS opponent_score,
                result * -1 AS result,
                total,
                overtime,
                old_game_id,
                gsis,
                nfl_detail_id,
                pfr,
                pff,
                espn,
                ftn,
                away_rest AS team_rest,
                home_rest AS opponent_rest,
                away_moneyline AS team_moneyline,
                home_moneyline AS opponent_moneyline,
                spread_line * -1 AS spread_line,
                away_spread_odds AS team_spread_odds,
                home_spread_odds AS opponent_spread_odds,
                total_line,
                under_odds,
                over_odds,
                div_game,
                roof,
                surface,
                wind,
                away_qb_id AS team_qb_id,
                away_qb_name AS team_qb_name,
                home_qb_id AS opponent_qb_id,
                home_qb_name AS opponent_qb_name,
                away_coach AS team_coach,
                home_coach AS opponent_coach,
                referee,
                stadium_id,
                stadium
            FROM
                nfl_schedule
            )
                
            SELECT 
                p.*
                ,p2.location AS prev_location
                ,p2.opponent AS prev_opponent
                ,p2.team_moneyline AS prev_team_moneyline
                ,p2.opponent_moneyline AS prev_opponent_moneyline
                ,p2.spread_line AS prev_spread_line
                ,p2.result AS prev_result
                ,p2.total_line AS prev_total_line
                ,p2.total AS prev_total
            FROM _pivot p
            LEFT JOIN _pivot p2 ON 
                p2.team = p.team
                AND p2.season = p.season
                AND p2.week = p.week - 1
            ORDER BY
                season DESC
                ,week DESC
                ,gametime DESC
                ,game_id DESC
                ,location DESC
        )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS _nfl_schedule');
    }
};
