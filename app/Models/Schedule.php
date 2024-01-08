<?php

namespace App\Models;

use App\Builders\ScheduleBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        $divisional = ($filters['divisional'] === 'divisional');

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

        Log::info($restLow);
        Log::info($restHigh);
    
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

        $spreadWins = $games->sum('bet_won');
        $spreadLosses = $games->sum('bet_lost');

        $unitsWon = collect($games)->sum(function ($item) {
            return (
                ($item->bet_won != 0 || $item->bet_lost != 0) ?
                    $item->bet_won == 1 
                    ? 
                        (intval($item->spread_odds) > 0
                        ?
                            intval($item->spread_odds)
                        :
                            100
                        )
                    :
                        (intval($item->spread_odds) > 0
                        ?
                            -100
                        :
                            $item->spread_odds
                        )
                :
                    0
            );
        }) / 100;

        $mlWins = collect($games)->sum(function ($item) {
            return (
                $item->mov > 0 ? 1 : 0
            );
        });
        $mlLosses = collect($games)->sum(function ($item) {
            return (
                $item->mov < 0 ? 1 : 0
            );
        });

        $mlUnitsWon = collect($games)->sum(function ($item) {
            return (
                ($item->mov != 0 ) ?
                    $item->mov > 0
                    ? 
                        (intval($item->moneyline_odds) > 0
                        ?
                            intval($item->moneyline_odds)
                        :
                            100
                        )
                    :
                        (intval($item->moneyline_odds) > 0
                        ?
                            -100
                        :
                            $item->moneyline_odds
                        )
                :
                    0
            );
        }) / 100;

        return [
            "ATS" => [
                "Win" => $spreadWins,
                "Loss" => $spreadLosses,
                "Units" => $unitsWon,
                "ROI" => round(($unitsWon / ($spreadWins + $spreadLosses) * 100),1)
            ],
            "SU" => [
                "Win" => $mlWins,
                "Loss" => $mlLosses,
                "Units" => $mlUnitsWon,
                "ROI" => round(($mlUnitsWon / ($mlWins + $mlLosses) * 100),1)
            ]
        ];
                
    }
}
