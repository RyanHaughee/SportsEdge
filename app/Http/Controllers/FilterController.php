<?php

namespace App\Http\Controllers;

use App\Models\SystemFilter;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function save(Request $request) {
        $filters = $request->input('filters');
        $newFilter = json_encode($filters);

        $savedFilter = new SystemFilter;
        $savedFilter->name = $request->input('name');
        $savedFilter->filters = $newFilter;
        $savedFilter->save();
    }

    public function get($id = null) {
        if ($id) {
            $filters = SystemFilter::find($id);
            $filters->filters = json_decode($filters->filters);
        } else {
            $filters = SystemFilter::select('id', 'name')->get()->values();
        }

        $response = [
            'success' => true,
            'filters' => $filters
        ];

        return $response;
    }
}
