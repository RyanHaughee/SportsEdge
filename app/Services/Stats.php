<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class Stats {
    function binomialPValue($trials, $probability, $successes) {
        $p_value = 0;
        $logProbability = log($probability);
        $log1MinusProbability = log(1 - $probability);
    
        for ($x = $successes; $x <= $trials; $x++) {
            $logProbabilityMassFunction =
                $this->logFactorial($trials) - $this->logFactorial($x)
                - $this->logFactorial($trials - $x)
                + ($x * $logProbability)
                + (($trials - $x) * $log1MinusProbability);
    
            $p_value += exp($logProbabilityMassFunction);
        }
    
        return $p_value;
    }
    
    function logFactorial($n) {
        return ($n <= 1) ? 0 : array_sum(array_map('log', range(1, $n)));
    }
}