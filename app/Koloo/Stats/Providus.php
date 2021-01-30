<?php

namespace App\Koloo\Stats;

use App\Koloo\Stats\Configurators\DateRangeQueryConfigurator;
use App\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class Providus
 *
 * @package \App\Koloo\Stats
 */
class Providus
{
    /**
     * Total amount received via providus channel
     *
     * @param \App\Koloo\Stats\DateRangeQueryConfigurator $configurator
     *
     * @return float
     */
    public static function getTotalInFlow(DateRangeQueryConfigurator $configurator) : float {
        try {
            $query = static::attachDateRange($configurator, Transaction::query());
            $result = $query->credit()->monnify()->sum('amount');
            return round($result/100, 2);

        } catch (\Exception $e) {
            Log::info('Invalid data: ' . $e->getMessage());
        }

        return -1;
    }

    protected static function attachDateRange(DateRangeQueryConfigurator $configurator, Builder $query) : Builder {
        $fromDate = $configurator->fromDate();
        $toDate = $configurator->toDate();

        if($fromDate || $toDate) {
            if($fromDate && $toDate)
                $query->whereDate($configurator->getField(), $configurator->fromDateOperator(), $fromDate)
                      ->whereDate($configurator->getField(), $configurator->toDateOperator(), $toDate);
            elseif ($fromDate)
                $query->whereDate($configurator->getField(), $configurator->fromDateOperator(), $fromDate);
            elseif ($toDate)
                $query->whereDate($configurator->getField(), $configurator->toDateOperator(), $toDate);
        }

        return $query;
    }

    public static function getCommissionEarnedByUser(DateRangeQueryConfigurator $configurator, \App\User $user) {
        try {
            $result = static::attachDateRange($configurator, $user->transactions()->getQuery())
                ->credit()->monnify()->where('remark', 'like', Transaction::TRANSFER_CHARGE_REASON .'%')->sum('amount');

            return round($result/100, 2);

        } catch (\Exception $e) {
            Log::info('Invalid data: ' . $e->getMessage());
        }

        return -1;
    }

    /**
     * Get credit transactions total for a user based on label. E.g withdrawal, commission  etc
     * @param \App\Koloo\Stats\Configurators\DateRangeQueryConfigurator $configurator
     * @param \App\User                                                 $user
     *
     * @return array
     */
    public static function getEarnedByUserViaLabel(DateRangeQueryConfigurator $configurator, \App\User $user) {
        try {
            $output = [];
            $labels = DB::table('transactions')->select('label')->where('label', '!=', Transaction::LABEL_MONNIFY)->groupBy('label')->get();
            $rootUser = \App\Koloo\User::rootUser();
            foreach ($labels as $label) {
                $query = static::attachDateRange($configurator, $user->transactions()->getQuery());
                if($user->id === $rootUser->getId()) {
                    $query->where('remark', '!=', Transaction::TRANSFER_CHARGE_REASON);
                }
                $result  =   $query->credit()->where('label', $label->label)->sum('amount');

                 round($result/100, 2);

                 $output[] = ['label' => $label->label, 'total' => round($result/100, 2)];
            }

            return $output;

        } catch (\Exception $e) {
            Log::info('Invalid data: ' . $e->getMessage());
        }

        return [];
    }
}

