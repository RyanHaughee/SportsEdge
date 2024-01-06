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
    protected $table = 'nfl_schedule';

    public function newEloquentBuilder($query): ScheduleBuilder
    {
        return new ScheduleBuilder($query);
    }

    public static function processFilters($results, $filters, $home) 
    {
        // SPREAD HIGH / LOW
        if (isset($filters['spread'])) {
            $results = self::processSpreadFilter($filters, $results, $home);
        }

        // WEEK HIGH / LOW
        if (isset($filters['week'])) {
            $results = self::processWeekFilter($filters, $results);
        }

        // WEEK HIGH / LOW
        if (isset($filters['total'])) {
            Log::info($filters['total']);
            $results = self::processTotalFilter($filters, $results);
        }

        // WEEK HIGH / LOW
        if (isset($filters['divisional'])) {
            $results = self::processDivisionalFilter($filters, $results);
        }

        return $results;
    }

    public static function processSpreadFilter($filters, $results, $home) {
        $spreadLow = isset($filters['spread']['low']) ? intval($filters['spread']['low']) : 100;
        $spreadHigh = isset($filters['spread']['high']) ? intval($filters['spread']['high']) : -100;

        return $results->spreadRange($spreadLow, $spreadHigh, $home);
    }

    public static function processWeekFilter($filters, $results) {
        $weekLow = isset($filters['week']['low']) ? intval($filters['week']['low']) : 0;
        $weekHigh = isset($filters['week']['high']) ? intval($filters['week']['high']) : 25;
    
        return $results->weekFilter($weekLow, $weekHigh);
    }

    public static function processTotalFilter($filters, $results) {
        $totalLow = isset($filters['total']['low']) ? intval($filters['total']['low']) : 0;
        $totalHigh = isset($filters['total']['high']) ? intval($filters['total']['high']) : 25;
    
        return $results->totalRange($totalLow, $totalHigh);
    }

    public static function processDivisionalFilter($filters, $results) {
        $divisional = ($filters['divisional'] === 'divisional');

        return $results->isDivisional($divisional);
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
