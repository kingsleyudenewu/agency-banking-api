<?php

namespace App\Koloo;

use App\Saving as Model;
use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


/**
 * Class SavingManagement
 *
 * @package \App\Koloo
 */
class SavingManagement
{

    public static function logInfo($message)
    {
        Log::channel('koloo')->info($message);
    }

    public static function getMaturedSavings()
    {
            $query  = Model::query();

            return $query->sweepables()->get();

    }

    public static function charge(Model $saving)
    {
        $cycle = $saving->cycle;
        $percentToCharge = doubleval($cycle->percentage_to_charge);
        $savingFrequency = $saving->saving_frequent_count;
        $customer = new User($saving->owner);

        /*
            We have turned off charges for savings --
            This is audacious but we will see how it goes.
            For now we complement for this by increasing the withdrawal charge. 
        */

        //Original logic for charging
        // if($percentToCharge === doubleval(0) &&
        //     ( $savingFrequency < $cycle->min_saving_frequent ) ) {
        //     $percentToCharge = settings('percent_to_charge') / 100;
        //     self::processCharge($saving, $percentToCharge, $customer);
        //    return ;//
        // }
        
        //Agent has been flagged for fraud and we have to charge all his savings
        $flaggedAgents = array(
            "aa5e0a60-c58e-11ea-bfd8-690616fa37f7"
        );

        if( $saving->owner && is_int(array_search($savings->owner->id, $flaggedAgents)) ){
            $percentToCharge = settings('percent_to_charge') / 100;
            self::processCharge($saving, $percentToCharge, $customer);

            return;
        }

        if($percentToCharge > 0)
        {
            self::processCharge($saving, $percentToCharge, $customer);
            return;
        }

        // The customer earned some profit
        try {
            DB::beginTransaction();

            $percentToEarn = abs($percentToCharge);
            $profitEarned = percentOf($saving->amount_saved, $percentToEarn);
            $totalEarned = $saving->amount_saved + $profitEarned;
            $chargeComment = sprintf('Earned N%.2f (%s%%) Total Saved N%.2f. Total credited: N%.2f',
                $profitEarned, $percentToEarn, $saving->amount_saved, $totalEarned);

            $customer->creditWalletSource($totalEarned, $customer->mainWallet(), $chargeComment, Transaction::LABEL_SWEEP);
            $saving->swept($chargeComment);
            static::logInfo($chargeComment);
            DB::commit();

        } catch (\Exception $e)
        {
            DB::rollBack();
            static::logInfo($e->getMessage());
        }
    }



    private static function calculateCharge($total, $percent)
    {
        return percentOf($total, $percent);
    }

    /**
     * @param \App\Saving     $saving
     * @param float           $percentToCharge
     * @param \App\Koloo\User $customer
     */
    private static function processCharge(Model $saving, float $percentToCharge, User $customer): void
    {
        DB::beginTransaction();
        try {
            $totalCharge = static::calculateCharge($saving->amount_saved, $percentToCharge);
            $earned = $saving->amount_saved - $totalCharge;
            $chargeComment = sprintf('Charged N%.2f (%s%%) Total Saved N%.2f. Total credited: N%.2f',
                $totalCharge, $percentToCharge, $saving->amount_saved, $earned);

            $customer->creditWalletSource($earned, $customer->mainWallet(), $chargeComment, Transaction::LABEL_SWEEP);
            $saving->swept($chargeComment);

            $rootUser = User::rootUser();
            $rootUser->creditWalletSource($totalCharge, $rootUser->mainWallet(), 'Earning from saving sweep: ' . $customer->getName(), Transaction::LABEL_SWEEP);

            static::logInfo($chargeComment);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            static::logInfo($e->getMessage());
        }
    }
}
