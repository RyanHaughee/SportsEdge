<?php

namespace App\Models;

use App\Builders\ScheduleBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\Stats;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = '_nfl_schedule';

    public function newEloquentBuilder($query): ScheduleBuilder
    {
        return new ScheduleBuilder($query);
    }

    public static function processFilters($results, $filters) 
    {
        // SPREAD HIGH / LOW
        if (isset($filters['spread'])) {
            $results = self::processSpreadFilter($filters, $results);
        }

        // WEEK HIGH / LOW
        if (isset($filters['week'])) {
            $results = self::processWeekFilter($filters, $results);
        }

        // WEEK HIGH / LOW
        if (isset($filters['total'])) {
            $results = self::processTotalFilter($filters, $results);
        }

        // WEEK HIGH / LOW
        if (isset($filters['divisional'])) {
            $results = self::processDivisionalFilter($filters, $results);
        }

        if(isset($filters['gametype'])) {
            $results = self::gameTypeFilter($filters, $results);
        }

        if(isset($filters['daysrest'])) {
            $results = self::filterRest($filters, $results);
        }

        if(isset($filters['opprest'])) {
            $results = self::filterOppRest($filters, $results);
        }

        if(isset($filters['homeaway'])) {
            $results = self::homeAwayFilter($filters, $results);
        }

        if(isset($filters['lastresult'])) {
            $results = self::lastResultFilter($filters, $results);
        }

        if(isset($filters['lastlocation'])) {
            $results = self::lastGameLocation($filters, $results);
        }

        return $results;
    }

    public static function processSpreadFilter($filters, $results) {
        $spreadLow = isset($filters['spread']['low']) ? number_format((float)$filters['spread']['low'], 1, '.', '') : 100;
        $spreadHigh = isset($filters['spread']['high']) ? number_format((float)$filters['spread']['high'], 1, '.', '') : -100;

        if ($spreadLow < $spreadHigh) {
            return $results->spreadRange($spreadLow, $spreadHigh);
        } else {
            return $results->spreadRange($spreadHigh, $spreadLow);
        }
    }

    public static function processWeekFilter($filters, $results) {
        $weekLow = isset($filters['week']['low']) ? intval($filters['week']['low']) : 0;
        $weekHigh = isset($filters['week']['high']) ? intval($filters['week']['high']) : 25;
    
        return $results->weekFilter($weekLow, $weekHigh);
    }

    public static function processTotalFilter($filters, $results) {
        $totalLow = isset($filters['total']['low']) ? intval($filters['total']['low']) : 0;
        $totalHigh = isset($filters['total']['high']) ? intval($filters['total']['high']) : 100;
    
        return $results->totalRange($totalLow, $totalHigh);
    }

    public static function processDivisionalFilter($filters, $results) {
        $divisional = ($filters['divisional'] === 'Yes');

        return $results->isDivisional($divisional);
    }

    public static function homeAwayFilter($filters, $results) {
        if (isset($filters['homeaway'])) {
            switch($filters['homeaway']) {
                case "home":
                    $location = ["=", "Home"];
                    break;
                case "away":
                    $location = ["=", "Away"];
                    break;
                case "neutral":
                    $location = ["=", "Neutral"];
                    break;
            }
        } else {
            $location = ["<>", "Home"];
        }

        return $results->homeAway($location);
    }

    public static function gameTypeFilter($filters, $results) {
        $regular_season = ($filters['gametype'] === 'regular');

        return $results->gameType($regular_season);
    }

    public static function filterRest($filters, $results) {
        $restLow = isset($filters['daysrest']['low']) ? intval($filters['daysrest']['low']) : 0;
        $restHigh = isset($filters['daysrest']['high']) ? intval($filters['daysrest']['high']) : 25;
    
        return $results->restFilter($restLow, $restHigh);
    }

    public static function filterOppRest($filters, $results) {
        $restLow = isset($filters['opprest']['low']) ? intval($filters['opprest']['low']) : 0;
        $restHigh = isset($filters['opprest']['high']) ? intval($filters['opprest']['high']) : 25;
    
        return $results->oppRestFilter($restLow, $restHigh);
    }

    public static function lastResultFilter($filters, $results) {
        $lastResult = $filters['lastresult'] == "covered" ?? false;
    
        return $results->lastGameResult($lastResult);
    }

    public static function lastGameLocation($filters, $results) {
        $lastLocation = $filters['lastlocation'] == "home" ? "Home" : "Away";
    
        return $results->lastGameLocation($lastLocation);
    }


    public static function computeGamblingRecord($games) {
        $returnObj['Total'] = [
            'ATS' => ['W' => 0, 'L' => 0, 'Units' => 0, 'Investment' => 0, 'ROI' => 0],
            'SU' => ['W' => 0, 'L' => 0, 'Units' => 0, 'Investment' => 0, 'ROI' => 0]
        ];
        
        foreach ($games as $game) {
            $season = $game->season;
        
            if (!isset($returnObj[$season])) {
                $returnObj[$season] = [
                    'ATS' => ['W' => 0, 'L' => 0, 'Units' => 0, 'Investment' => 0, 'ROI' => 0],
                    'SU' => ['W' => 0, 'L' => 0, 'Units' => 0, 'Investment' => 0, 'ROI' => 0]
                ];
            }
        
            // ATS calculation
            $returnObj['Total']['ATS']['W'] += $game->bet_won;
            $returnObj['Total']['ATS']['L'] += $game->bet_lost;
            $returnObj[$season]['ATS']['W'] += $game->bet_won;
            $returnObj[$season]['ATS']['L'] += $game->bet_lost;
        
            // SU calculation
            $returnObj['Total']['SU']['W'] += $game->mov > 0 ? 1 : 0;
            $returnObj['Total']['SU']['L'] += $game->mov < 0 ? 1 : 0;
            $returnObj[$season]['SU']['W'] += $game->mov > 0 ? 1 : 0;
            $returnObj[$season]['SU']['L'] += $game->mov < 0 ? 1 : 0;
        
            // Units calculation
            if ($game->bet_won != 0 || $game->bet_lost != 0) {
                $spreadOdds = (intval($game->spread_odds) / 100);
                if ($game->bet_won == 1) {
                    $unitsChange = ($spreadOdds > 0) ? $spreadOdds : 1;
                } else {
                    $unitsChange = ($spreadOdds > 0) ? -1 : $spreadOdds;
                }
        
                $returnObj['Total']['ATS']['Investment'] += ($spreadOdds > 0) ? 1 : abs($spreadOdds);
                $returnObj['Total']['ATS']['Units'] += $unitsChange;
                $returnObj[$season]['ATS']['Investment'] += ($spreadOdds > 0) ? 1 : abs($spreadOdds);
                $returnObj[$season]['ATS']['Units'] += $unitsChange;
            }

            if($game->mov != 0) {
                $moneyLineOdds = (intval($game->moneyline_odds) / 100);
                if ($game->mov > 0) {
                    $unitChange = $moneyLineOdds > 0 ? $moneyLineOdds : 1;
                } else {
                    $unitChange = $moneyLineOdds > 0 ? -1 : $moneyLineOdds;
                }

                $returnObj['Total']['SU']['Investment'] += ($moneyLineOdds > 0) ? 1 : abs($moneyLineOdds);
                $returnObj['Total']['SU']['Units'] += $unitsChange;
                $returnObj[$season]['SU']['Investment'] += ($moneyLineOdds > 0) ? 1 : abs($moneyLineOdds);
                $returnObj[$season]['SU']['Units'] += $unitsChange;
            }


        }

        foreach($returnObj as $year => $obj) {
            $returnObj[$year]['ATS']['ROI'] = $obj['ATS']['Investment'] != 0 ? number_format(($obj['ATS']['Units'] / $obj['ATS']['Investment'] * 100),1) : 0;
            $returnObj[$year]['SU']['ROI'] = $obj['ATS']['Investment'] != 0 ? number_format(($obj['SU']['Units'] / $obj['SU']['Investment'] * 100),1) : 0;

            $returnObj[$year]['ATS']['Units'] = number_format($obj['ATS']['Units'], 1);
            $returnObj[$year]['SU']['Units'] = number_format($obj['SU']['Units'], 1);
        }

        // Define parameters for the binomial distribution
        $trials = ($returnObj['Total']['ATS']['W'] + $returnObj['Total']['ATS']['L']); // Number of trials
        $probability = 0.524; // Probability of success for each trial
        $successes = $returnObj['Total']['ATS']['W']; // Observed number of successes

        $statsService = new Stats();
        $p_value = 1 - $statsService->binomialPValue($trials, $probability, $successes);

        // Calculate the p-value
        $grade = number_format($p_value*100,0);


        // Calculate p-value using CDF
        $returnObj['Total']['ATS']['Grade'] = number_format($grade,0);

        return $returnObj;
                
    }
}
