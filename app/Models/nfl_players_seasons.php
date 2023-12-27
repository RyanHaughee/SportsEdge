<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class nfl_players_seasons extends Model
{
    protected $fillable = [
        'gsis_id',
        'espn_id',
        'sportradar_id',
        'yahoo_id',
        'rotowire_id',
        'pff_id',
        'pfr_id',
        'fantasy_data_id',
        'sleeper_id',
        'esb_id',
        'gsis_it_id',
        'smart_id',
        'headshot_url',
        'season',
        'team',
        'first_name',
        'last_name',
        'full_name'
    ];
}
