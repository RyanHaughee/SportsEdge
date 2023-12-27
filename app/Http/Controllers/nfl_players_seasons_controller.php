<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\nfl_players_seasons;

class nfl_players_seasons_controller extends Controller
{
    public function importData(Request $request)
    {
        $data = $request->all();

        // Assuming the request data is an array of player data
        foreach ($data as $playerData) {
            nfl_players_seasons::create($playerData);
        }

        return response()->json(['message' => 'Data imported successfully']);
    }
}
