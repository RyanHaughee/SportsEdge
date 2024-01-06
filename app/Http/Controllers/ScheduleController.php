<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function fetchAll(Request $request) {
        $filters = $request->input('filters');

        if(!empty($filters)){
            $location_array = ["home", "away"];
        } else {
            $location_array = ["away"];
        }
        $results = [];

        foreach($location_array as $index => $location) {
            $home = $location === "home";
            $results[$index] = Schedule::query()
            ->selectHomeAway($home)
            ->joinTeams($home);

            $results[$index] = Schedule::processFilters($results[$index], $filters, $home);


            $results[$index] = $results[$index]
            ->orderResults()
            ->get();
        } 



        if (isset($results[1]))
        {
            $merged = $results[0]->concat($results[1]);
        } else {
            $merged = $results[0];
        }

        $merged = $merged->sortByDesc('id');

        $result = [];
        $result['record'] = Schedule::computeGamblingRecord($merged);
        $result['games'] = $merged->values()->take(50);

        return $result;
    }
}