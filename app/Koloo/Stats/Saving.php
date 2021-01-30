<?php

namespace App\Koloo\Stats;

use App\SavingCycle;

/**
 * Class Saving
 *
 * @package \App\Koloo\Stats
 */
class Saving {

    public static function getTotalSavingsCycleImmatureContribution(bool $onlyActive = true)  : array {
        $output = [];
        foreach(SavingCycle::select('id', 'title')->get() as $cycle) {
            $output[]  = [
                        'total_contribution' => static::getImmatureContributionForACycle($cycle, $onlyActive),
                        'name' => $cycle->title,
                        'id' => $cycle->id
            ];
        }

        return $output;
    }

    /**
     * @param \App\SavingCycle $cycle
     * @param bool             $onlyActive if is active, fetch only active savings else fetch matured savings
     *
     * @return float
     */
    protected static function getImmatureContributionForACycle(SavingCycle  $cycle, $onlyActive = true) : float {
        $total = 0;
        $savings = $onlyActive ? $cycle->savings()->active()->get() : $cycle->savings()->completed()->get();
        foreach ($savings as $saving) {
            $total += $saving->getAmountSaved();
        }
        return $total;
    }
}
