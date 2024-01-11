<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\SystemFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function fetchAll(Request $request) {
        $filters = $request->input('filters');

        // $results = Schedule::query()
        //     ->selectColumns()
        //     ->joinTeams()
        //     ->where('season','>=',2019)
        //     ->where('season','<=',2023);

        // $results = Schedule::query()
        //     ->selectColumns()
        //     ->joinTeams()
        //     ->where('season','<=',2022);

        $results = Schedule::query()
            ->selectColumns()
            ->joinTeams()
            ->where('season','>=',2014);

        $results = Schedule::processFilters($results, $filters);

        $results = $results->orderResults()->get()->sortByDesc('id');

        $result = [];
        $result['record'] = Schedule::computeGamblingRecord($results);
        // $result['games'] = $results->values()->take(50);
        $result['games'] = $results->values();
        return $result;
    }
}
