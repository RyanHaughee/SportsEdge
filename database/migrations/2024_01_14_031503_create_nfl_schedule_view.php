<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateNflScheduleView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE OR REPLACE VIEW _nfl_schedule AS
        
        -- CTE Storing Weekly Results
        WITH _pivot AS (
            -- Select home team data
            SELECT
                game_id,
                season,
                game_type,
                week,
                gameday,
                weekday,
                gametime,
                home_team AS team,
                CASE WHEN location = "Neutral" THEN "Neutral" ELSE "Home" END AS location,
                away_team AS opponent,
                home_score AS team_score,
                away_score AS opponent_score,
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
                home_rest AS team_rest,
                away_rest AS opponent_rest,
                home_moneyline AS team_moneyline,
                away_moneyline AS opponent_moneyline,
                spread_line * -1 AS spread_line,
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
        
            -- Select away team data
            SELECT
                game_id,
                season,
                game_type,
                week,
                gameday,
                weekday,
                gametime,
                away_team AS team,
                CASE WHEN location = "Neutral" THEN "Neutral" ELSE "Away" END AS location,
                home_team AS opponent,
                away_score AS team_score,
                home_score AS opponent_score,
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
                away_rest AS team_rest,
                home_rest AS opponent_rest,
                away_moneyline AS team_moneyline,
                home_moneyline AS opponent_moneyline,
                spread_line AS spread_line,
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
        ),
        
        -- Get each teams last week played to avoid join issues caused by bye weeks
        _prev_week AS (
            SELECT
                team,
                season,
                week,
                LAG(week) OVER (PARTITION BY team, season ORDER BY week) AS prev_week
            FROM
                _pivot
        )
        
        -- Final product
        SELECT
            p.game_id,
            p.season,
            p.game_type,
            p.week,
            p.gameday,
            p.weekday,
            p.gametime,
            p.team,
            p.location,
            p.opponent,
            p.team_score,
            p.opponent_score,
            p.result,
            p.total,
            p.overtime,
            p.team_rest,
            p.opponent_rest,
            p.team_moneyline,
            p.opponent_moneyline,
            p.spread_line,
            p.team_spread_odds,
            p.opponent_spread_odds,
            p.total_line,
            p.under_odds,
            p.over_odds,
            p.div_game,
            p.roof,
            p.surface,
            p.wind,
            p.team_qb_id,
            p.team_qb_name,
            p.opponent_qb_id,
            p.opponent_qb_name,
            p.team_coach,
            p.opponent_coach,
            p.referee,
            p.stadium_id,
            p.stadium,
            cf.win_pct AS team_win_pct,
            cf.win_loss_streak AS team_win_loss_streak,
            cf.cover_streak AS team_cover_streak,
            cf.total_streak AS team_total_streak,
            cf2.win_pct AS opponent_win_pct,
            cf2.win_loss_streak AS opponent_win_loss_streak,
            cf2.cover_streak AS opponent_cover_streak,
            cf2.total_streak AS opponent_total_streak,
            p2.location AS prev_location,
            p2.opponent AS prev_opponent,
            p2.team_moneyline AS prev_team_moneyline,
            p2.opponent_moneyline AS prev_opponent_moneyline,
            p2.spread_line AS prev_spread_line,
            p2.result AS prev_result,
            p2.total_line AS prev_total_line,
            p2.total AS prev_total
        FROM 
            _pivot p
        JOIN 
            _prev_week pw ON pw.team = p.team AND pw.season = p.season AND pw.week = p.week 
        LEFT JOIN 
            nfl_calculated_fields cf ON cf.team = p.team AND cf.season = pw.season AND cf.week = pw.prev_week
        LEFT JOIN 
            nfl_calculated_fields cf2 ON cf2.team = p.opponent AND cf2.season = pw.season AND cf2.week = pw.prev_week
        LEFT JOIN 
            _pivot p2 ON p2.team = pw.team AND p2.season = pw.season AND p2.week = pw.prev_week
        ORDER BY
            p.season DESC,
            p.week DESC,
            p.gametime DESC,
            p.game_id DESC,
            p.location DESC
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS _nfl_schedule');
    }
}
