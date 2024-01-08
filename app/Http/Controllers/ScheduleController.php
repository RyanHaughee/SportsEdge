<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function fetchAll(Request $request) {
        $filters = $request->input('filters');

        Log::info($filters);

        $results = Schedule::query()
            ->selectColumns()
            ->joinTeams();

        $results = Schedule::processFilters($results, $filters);

        $results = $results
            ->orderResults()
            ->get();

        $results = $results->sortByDesc('id');

        $result = [];
        $result['record'] = Schedule::computeGamblingRecord($results);
        $result['games'] = $results->values()->take(50);

        return $result;
    }
}
