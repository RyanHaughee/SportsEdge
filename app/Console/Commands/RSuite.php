<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RSuite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:r-suite';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    { 
        $scriptPaths = [
            'def_adv_stats.R',
            'kick_stats.R',
            'off_adv_stats.R',
            'off_nextgen_stats.R',
            'off_stats.R',
            'player_snaps.R',
            'players.R',
            'schedule.R',
            'teams.R'
        ]; // R scripts inside the scripts/R directory
        $scriptDirectory = base_path('scripts/R'); // Directory containing R scripts

        // Change directory to the R script directory
        chdir($scriptDirectory);

        foreach ($scriptPaths as $script)
        {
            $command = "Rscript {$script}"; // Command to execute R script

            exec($command, $output, $returnVar);
    
            if ($returnVar === 0) {
                $this->info($script.' executed successfully.');
            } else {
                $this->error('Failed to execute '.$script);
            } 
        }

        DB::select('CALL NFLCalculateFields');
        
    }
}
