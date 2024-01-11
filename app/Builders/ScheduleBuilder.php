<?php

namespace App\Builders;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Builder;

class ScheduleBuilder extends Builder
{
    /**
     * Select columns HERE!
     *
     */
    public function selectColumns() {
        return $this->selectRaw('
            _nfl_schedule.game_id,
            _nfl_schedule.season,
            _nfl_schedule.week,
            _nfl_schedule.team_score,
            _nfl_schedule.opponent_score as opp_score,
            IF(_nfl_schedule.location = "Home", 1, 0) as home,
            team.team_name as team,
            opp_team.team_name as opp,
            (_nfl_schedule.spread_line * -1) as spread,
            team.team_logo_espn as team_logo,
            opp_team.team_logo_espn as opp_team_logo,
            IF(_nfl_schedule.result > _nfl_schedule.spread_line, 1, 0) AS bet_won,
            IF(_nfl_schedule.result < _nfl_schedule.spread_line, 1, 0) AS bet_lost,
            _nfl_schedule.team_spread_odds as spread_odds,
            _nfl_schedule.team_moneyline as moneyline_odds,
            _nfl_schedule.result as mov
        ');
    }

    /**
     * Create spread range where statement
     *
     * @param int $low The lower end of the spread range, the HIGHER number
     * @param int $high The higher end of the spread range, the LOWER number
     */
    public function spreadRange($low, $high) {
        return $this->where('_nfl_schedule.spread_line','<=',(-1*$low))->where('_nfl_schedule.spread_line','>=',(-1*$high));
    }

    /**
     * Create total range where statement
     *
     * @param int $low The lower end of the total range
     * @param int $high The higher end of the total range
     */
    public function totalRange($low, $high) {
        return $this->where('_nfl_schedule.total_line','>=',$low)->where('_nfl_schedule.total_line','<=',$high);
    }

    /**
     * Filter by week
     *
     * @param int $low lower week
     * @param int $high higher week
     */
    public function weekFilter($low, $high) {
        return $this->where('_nfl_schedule.week','>=',$low)->where('_nfl_schedule.week','<=',$high);
    }

    /**
     * Default Order
     *
     */
    public function orderResults() {
        return $this->orderBy('_nfl_schedule.season','DESC')->orderBy('_nfl_schedule.week','DESC');
    }

    /**
     * Join Team Table
     *
     */
    public function joinTeams() { 
        return $this->join('nfl_teams as team', 'team.team_abbr', '_nfl_schedule.team')
            ->join('nfl_teams as opp_team', 'opp_team.team_abbr', '_nfl_schedule.opponent');
    }

    /**
     * Divisional Game
     *
     */
    public function isDivisional($true) {
        return $this->where('_nfl_schedule.div_game',$true);
    }

    /**
     * Home or Away?
     *
     */
    public function homeAway($location) {
        return $this->where('_nfl_schedule.location',$location[0], $location[1]);
    }    

    public function gameType($regular_season) {
        $equal = $regular_season ? '=' : '<>';
            
        return $this->where('_nfl_schedule.game_type',$equal,'REG');
    }

    /**
     * Filter by rest
     *
     * @param int $low lower rest
     * @param int $high higher rest
     */
    public function restFilter($low, $high) {
        return $this->where('_nfl_schedule.team_rest','>=',$low)->where('_nfl_schedule.team_rest','<=',$high);
    }

    /**
     * Filter by opponent rest
     *
     * @param int $low lower rest
     * @param int $high higher rest
     */
    public function oppRestFilter($low, $high) {
        return $this->where('_nfl_schedule.opponent_rest','>=',$low)->where('_nfl_schedule.opponent_rest','<=',$high);
    }

    /**
     * Filter by opponent rest
     *
     * @param int $low lower rest
     * @param int $high higher rest
     */
    public function lastGameResult($cover) {
        if ($cover) {
            return $this->whereRaw('_nfl_schedule.prev_result > _nfl_schedule.prev_spread_line');
        } else {
            return $this->whereRaw('_nfl_schedule.prev_result < _nfl_schedule.prev_spread_line');
        }
    }

    /**
     * Filter by opponent rest
     *
     * @param int $low lower rest
     * @param int $high higher rest
     */
    public function lastGameLocation($location) {
        return $this->where('_nfl_schedule.prev_location',$location);
    }

    public function prevResultFilter($low, $high) {
        return $this->where('_nfl_schedule.prev_result','>=',$low)->where('_nfl_schedule.prev_result','<=',$high);
    }

    public function oppLastGameResult($cover) {
        if ($cover) {
            return $this->whereRaw('_nfl_schedule.opp_prev_result > _nfl_schedule.opp_prev_spread_line');
        } else {
            return $this->whereRaw('_nfl_schedule.opp_prev_result < _nfl_schedule.opp_prev_spread_line');
        }
    }

    /**
     * Filter by opponent rest
     *
     * @param int $low lower rest
     * @param int $high higher rest
     */
    public function oppLastGameLocation($location) {
        return $this->where('_nfl_schedule.opp_prev_location',$location);
    }

    public function oppPrevResultFilter($low, $high) {
        return $this->where('_nfl_schedule.opp_prev_result','>=',$low)->where('_nfl_schedule.opp_prev_result','<=',$high);
    }
}