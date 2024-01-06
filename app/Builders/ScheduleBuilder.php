<?php

namespace App\Builders;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Builder;

class ScheduleBuilder extends Builder
{
    /**
     * Select columns HERE!
     *
     * @param boolean $home Spread is flipped if true
     */
    public function selectHomeAway($home = false) {
        if ($home === true){
            return $this->selectRaw('
                nfl_schedule.id,
                nfl_schedule.game_id,
                nfl_schedule.season,
                nfl_schedule.week,
                nfl_schedule.home_score as team_score,
                nfl_schedule.away_score as opp_score,
                1 as home,
                team.team_name as team,
                opp_team.team_name as opp,
                (nfl_schedule.spread_line * -1) as spread,
                team.team_logo_espn as team_logo,
                opp_team.team_logo_espn as opp_team_logo,
                IF(nfl_schedule.result > nfl_schedule.spread_line, 1, 0) AS bet_won,
                IF(nfl_schedule.result < nfl_schedule.spread_line, 1, 0) AS bet_lost,
                nfl_schedule.home_spread_odds as spread_odds,
                nfl_schedule.home_moneyline as moneyline_odds,
                nfl_schedule.result as mov
            ');
        } else {
            return $this->selectRaw('
                nfl_schedule.id,
                nfl_schedule.game_id,
                nfl_schedule.season,
                nfl_schedule.week,
                nfl_schedule.away_score as team_score,
                nfl_schedule.home_score as opp_score,
                0 as home,
                team.team_name as team,
                opp_team.team_name as opp,
                nfl_schedule.spread_line as spread,
                team.team_logo_espn as team_logo,
                opp_team.team_logo_espn as opp_team_logo,
                IF(nfl_schedule.result < nfl_schedule.spread_line, 1, 0) as bet_won,
                IF(nfl_schedule.result > nfl_schedule.spread_line, 1, 0) as bet_lost,
                nfl_schedule.away_spread_odds as spread_odds,
                nfl_schedule.away_moneyline as moneyline_odds,
                (nfl_schedule.result * -1) as mov
            ');
        }
    }

    /**
     * Create spread range where statement
     *
     * @param int $low The lower end of the spread range, the HIGHER number
     * @param int $high The higher end of the spread range, the LOWER number
     * @param boolean $home Spread is flipped if true
     */
    public function spreadRange($low, $high, $home = false) {
        if ($home === true) {
            return $this->where('nfl_schedule.spread_line','>=',(-1*$low))->where('nfl_schedule.spread_line','<=',(-1*$high));
        } else {
            return $this->where('nfl_schedule.spread_line','<=',$low)->where('nfl_schedule.spread_line','>=',$high);
        }
    }

    /**
     * Create total range where statement
     *
     * @param int $low The lower end of the total range
     * @param int $high The higher end of the total range
     */
    public function totalRange($low, $high) {
        return $this->where('nfl_schedule.total_line','>=',$low)->where('nfl_schedule.total_line','<=',$high);
    }

    /**
     * Filter by week
     *
     * @param int $low lower week
     * @param int $high higher week
     */
    public function weekFilter($low, $high) {
        return $this->where('nfl_schedule.week','>=',$low)->where('nfl_schedule.week','<=',$high);
    }

    /**
     * Default Order
     *
     */
    public function orderResults() {
        return $this->orderBy('nfl_schedule.season','DESC')->orderBy('nfl_schedule.week','DESC');
    }

    /**
     * Join Team Table
     *
     * @param boolean $home = is the team we're looking for a home team?
     */
    public function joinTeams($home = false) { 
        if ($home === true) {
            return $this->join('nfl_teams as team', 'team.team_abbr', 'nfl_schedule.home_team')
                ->join('nfl_teams as opp_team', 'opp_team.team_abbr', 'nfl_schedule.away_team');
        } else {
            return $this->join('nfl_teams as team', 'team.team_abbr', 'nfl_schedule.away_team')
                ->join('nfl_teams as opp_team', 'opp_team.team_abbr', 'nfl_schedule.home_team');
        }
    }

    /**
     * Favorites or Dogs
     *
     * @param string $type = favorite || dog
     * @param boolean $home = is the team we're looking for a home team?
     */
    public function favoriteOrDog($type, $home) {
        if (
            ($home === true && $type === 'favorite') ||
            ($home === false && $type === 'dog')
        ) {
            return $this->where('nfl_schedule.spread_line','>',0);
        } else {
            return $this->where('nfl_schedule.spread_line','<',0);
        }
    }

    /**
     * Divisional Game
     *
     */
    public function isDivisional($true) {
        return $this->where('nfl_schedule.div_game',$true);
    }
}