<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateNflCalculatedFieldsProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE PROCEDURE NFLCalculateFields()
        BEGIN
            -- Create the nfl_calculated_fields table if it doesnt exist
            DROP TABLE IF EXISTS nfl_calculated_fields;
            CREATE TABLE IF NOT EXISTS nfl_calculated_fields (
                id INT AUTO_INCREMENT PRIMARY KEY,
                season INT,
                week INT,
                team VARCHAR(255),
                win_pct FLOAT,
                win_loss_streak INT,
                cover_streak INT,
                total_streak INT
            ) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
            
            -- Add indexes
            CREATE INDEX nfl_calculated_fields_team_index ON nfl_calculated_fields (team);
        
            -- Temporary tables
            DROP TABLE IF EXISTS _pivot;
            CREATE TEMPORARY TABLE _pivot
                SELECT DISTINCT
                    season,
                    week,
                    home_team AS team,
                    result * -1 AS result,
                    spread_line * -1 AS spread_line,
                    total,
                    total_line
                FROM
                    nfl_schedule
        
                UNION ALL
        
                -- Select away team data
                SELECT DISTINCT
                    season,
                    week,
                    away_team AS team,
                    result AS result,
                    spread_line AS spread_line,
                    total,
                    total_line
                FROM
                    nfl_schedule;
        
            -- Get each teams last week played to avoid join issues caused by bye weeks
           CREATE TEMPORARY TABLE _prev_week
               SELECT
                   team,
                   season,
                   week,
                   LAG(week) OVER (PARTITION BY team ORDER BY season, week) AS prev_week,
                   LAG(season) OVER (PARTITION BY team ORDER BY season, week) AS prev_season
                 FROM
                   _pivot;
        
            -- Calculate each teams current win/loss streak for each given week
            DROP TABLE IF EXISTS _win_streaks;   
            CREATE TEMPORARY TABLE _win_streaks
                SELECT
                    ws.team,
                    ws.season,
                    ws.week,
                    ws.result,
                    pw.prev_week,
                    @current_win_streak := IF(ws.result < 0, @current_win_streak + 1, 0) AS current_win_streak,
                    @current_losing_streak := IF(ws.result > 0, @current_losing_streak + 1, 0) AS current_losing_streak
                FROM
                    (SELECT team, season, week, result FROM _pivot ORDER BY team, season, week) AS ws
                JOIN
                    _prev_week pw ON ws.team = pw.team AND ws.season = pw.season AND ws.week = pw.week
                JOIN
                    (SELECT @current_win_streak := 0, @current_losing_streak := 0, @prev_team := NULL, @prev_season := NULL) AS init
                WHERE
                    (@prev_team := ws.team) IS NOT NULL
                    AND (@prev_season := ws.season) IS NOT NULL
                ORDER BY
                    ws.team, ws.season, ws.week;
                
            -- Calculate win percentage for each team at every week of the season
            DROP TABLE IF EXISTS _win_pct;
            CREATE TEMPORARY TABLE _win_pct
                SELECT
                    ws.team,
                    ws.season,
                    ws.week,
                    SUM(CASE WHEN ws.result < 0 THEN 1 ELSE 0 END) OVER (PARTITION BY ws.team, ws.season ORDER BY ws.week) AS total_wins,
                    COUNT(*) OVER (PARTITION BY ws.team, ws.season ORDER BY ws.week) AS total_games,
                    SUM(CASE WHEN ws.result < 0 THEN 1 ELSE 0 END) OVER (PARTITION BY ws.team, ws.season ORDER BY ws.week)
                    / COUNT(*) OVER (PARTITION BY ws.team, ws.season ORDER BY ws.week) AS win_pct
                FROM
                    _win_streaks ws;
        
            -- Calculate each teams current cover streak for each given week
            DROP TABLE IF EXISTS _cover_streaks;   
            CREATE TEMPORARY TABLE _cover_streaks
                SELECT
                    ws.team,
                    ws.season,
                    ws.week,
                    ws.result,
                    ws.spread_line,
                    @current_win_streak := IF(
                        (ws.spread_line < 0 AND ws.result < ws.spread_line) OR
                        (ws.spread_line > 0 AND ws.result < ws.spread_line),
                        @current_win_streak + 1, 0
                    ) AS current_win_streak,
                    @current_losing_streak := IF(
                        (ws.spread_line < 0 AND ws.result > ws.spread_line) OR
                        (ws.spread_line > 0 AND ws.result > ws.spread_line),
                        @current_losing_streak + 1, 0
                    ) AS current_losing_streak
                FROM
                    (SELECT team, season, week, result, spread_line FROM _pivot ORDER BY team, season, week) AS ws
                JOIN
                    _prev_week pw ON ws.team = pw.team AND ws.season = pw.season AND ws.week = pw.week
                JOIN
                    (SELECT @current_win_streak := 0, @current_losing_streak := 0, @prev_team := NULL, @prev_season := NULL) AS init
                WHERE
                    (@prev_team := ws.team) IS NOT NULL
                    AND (@prev_season := ws.season) IS NOT NULL
                ORDER BY
                    ws.team, ws.season, ws.week;
        
            -- Calculate each teams current rolling over/under streak for each given week
            DROP TABLE IF EXISTS _total_streaks;   
            CREATE TEMPORARY TABLE _total_streaks
                SELECT
                    ws.team,
                    ws.season,
                    ws.week,
                    ws.total,
                    ws.total_line,
                    @current_over_streak := IF(ws.total > ws.total_line, @current_over_streak + 1, 0) AS current_over_streak,
                    @current_under_streak := IF(ws.total < ws.total_line, @current_under_streak + 1, 0) AS current_under_streak
                FROM
                    (SELECT team, season, week, total, total_line FROM _pivot ORDER BY team, season, week) AS ws
                JOIN
                    _prev_week pw ON ws.team = pw.team AND ws.season = pw.season AND ws.week = pw.week
                JOIN
                    (SELECT @current_over_streak := 0, @current_under_streak := 0, @prev_team := NULL, @prev_season := NULL) AS init
                WHERE
                    (@prev_team := ws.team) IS NOT NULL
                    AND (@prev_season := ws.season) IS NOT NULL
                ORDER BY
                    ws.team, ws.season, ws.week;
        
            -- Final product
            INSERT INTO nfl_calculated_fields (season, week, team, win_pct, win_loss_streak, cover_streak, total_streak)
            SELECT
                p.season,
                p.week,
                p.team,
                wp.win_pct,
                CASE
                    WHEN ws.current_win_streak = 0 AND ws.current_losing_streak = 0 THEN 0
                    WHEN ws.current_win_streak = 0 THEN ws.current_losing_streak * -1
                    ELSE ws.current_win_streak
                END AS win_loss_streak,
                CASE
                    WHEN cs.current_win_streak = 0 AND cs.current_losing_streak = 0 THEN 0
                    WHEN cs.current_win_streak = 0 THEN cs.current_losing_streak * -1
                    ELSE cs.current_win_streak
                END AS cover_streak,
                CASE
                    WHEN ts.current_over_streak = 0 AND ts.current_under_streak = 0 THEN 0
                    WHEN ts.current_over_streak = 0 THEN ts.current_under_streak * -1
                    ELSE ts.current_over_streak
                END AS total_streak
            FROM 
                _pivot p
            JOIN 
                _prev_week pw ON pw.team = p.team AND pw.season = p.season AND pw.week = p.week 
            LEFT JOIN 
                _win_streaks ws ON ws.team = pw.team AND ws.season = pw.prev_season AND ws.week = pw.prev_week
            LEFT JOIN
                _win_pct wp ON wp.team = p.team AND wp.season = pw.season AND wp.week = pw.prev_week 
            LEFT JOIN 
                _cover_streaks cs ON cs.team = pw.team AND cs.season = pw.prev_season AND cs.week = pw.prev_week
            LEFT JOIN 
                _total_streaks ts ON ts.team = pw.team AND ts.season = pw.prev_season AND ts.week = pw.prev_week
            ORDER BY
                p.season DESC,
                p.week DESC;
            
            -- Drop temporary tables
            DROP TEMPORARY TABLE IF EXISTS _pivot;
            DROP TEMPORARY TABLE IF EXISTS _prev_week;
            DROP TEMPORARY TABLE IF EXISTS _win_streaks;
            DROP TEMPORARY TABLE IF EXISTS _win_pct;
            DROP TEMPORARY TABLE IF EXISTS _cover_streaks;
            DROP TEMPORARY TABLE IF EXISTS _total_streaks;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS NFLCalculateFields');
    }
}
