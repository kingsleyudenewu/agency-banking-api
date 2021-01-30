<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\APIBaseController;
use App\Koloo\Stats\Configurators\DateRange;
use App\Koloo\Stats\Providus;
use App\Koloo\Stats\Saving;
use App\Koloo\Stats\User;
use App\SavingCycle;

/**
 * Class AdminStatsController
 *
 * @package \App\Http\Controllers\Api\Admin
 */
class AdminStatsController extends APIBaseController {

    public function index()
    {
        $fromDate  = request('fromDate') ?: '';
        $toDate = request('toDate') ?: '';
        $dateRangeConfig =  new DateRange($fromDate, $toDate);

        return [
            'total_providus_inflow' =>  Providus::getTotalInFlow($dateRangeConfig),
            'total_providus_commission_earned' => Providus::getCommissionEarnedByUser($dateRangeConfig, \App\Koloo\User::rootUser()->getModel()),
            'labels' => Providus::getEarnedByUserViaLabel($dateRangeConfig, \App\Koloo\User::rootUser()->getModel())
        ];
    }


    public function users()
    {

        return [
            'agents' =>  User::agentsStats(),
            'super_agents' =>  User::superAgentStats(),
            'total_customers' =>  User::totalCustomers(),
            'total_admins' =>  User::totalAdmins(),
            'total_customer_balance' => User::totalCustomerWalletBalance(),
            'total_agents_balance' => User::totalAgentsWalletBalance(),
            'commissions' => User::commissionStats(),
        ];
    }

    public function savings() {
        $onlyActive = request('status') !== 'completed' ? true  : false;
        return [
            'total_immature_savings' => Saving::getTotalSavingsCycleImmatureContribution($onlyActive),
        ];
    }
}
