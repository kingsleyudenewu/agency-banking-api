<?php

namespace App\Koloo\Stats;

use  \App\User as UserModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class User
 *
 * @package \App\Koloo\Stats
 */
class User  {


    protected static function getUserCountByRole(string $role) : int {
        return static::getUsersByRole($role)->count();
    }



    protected static function getUsersByRole(string $role): Builder {
        return UserModel::select('id', 'name', 'parent_id', 'created_at', 'updated_at')->with('roles')
                        ->whereHas('roles', function($query) use ($role){
            $query->where('name', $role);
        });
    }

    public static function agentsStats() : array {

        $users = static::getUsersByRole(UserModel::ROLE_AGENT)->get();
        $totalActive = 0;
        foreach($users as $user) {
            $hasSaving = $user->savings()->active()->limit(1)
                                 ->count();
            if($hasSaving) {
                $totalActive++;
            }
        }

        return [
            'total' => $users->count(),
            'active' => $totalActive
        ];
    }

    public static function superAgentStats() : array {
        $users = static::getUsersByRole(UserModel::ROLE_SUPER_AGENT)->get();
        $totalActive = 0;
        foreach($users as $user) {
            if($user->transactions()->credit()->limit(1)->count()) {
                $totalActive++;
            }
        }

        return [
            'total' => $users->count(),
            'active' => $totalActive
        ];
    }

    public static function commissionStats(): array {

        return [
            'total_commission_earned_by_platform' => \App\Koloo\User::rootUser()->getModel()->totalCommissionEarned(),
            'total_commission_earned_by_agent' => static::getTotalCommissionEarnedByRole(\App\User::ROLE_AGENT),
            'total_commission_earned_by_super_agent' => static::getTotalCommissionEarnedByRole(\App\User::ROLE_SUPER_AGENT)
        ];
    }

    protected static function getTotalCommissionEarnedByRole(string $role)  : float {
        $users = static::getUsersByRole($role)->get();
        $total = 0;
        foreach ($users as $user) {
            $total += $user->totalCommissionEarned();
        }
        return $total;
    }

    public  static function totalAdmins() : int {
        return static::getUserCountByRole(UserModel::ROLE_ADMIN);
    }

    public static function totalCustomers() : int {
        return static::getUserCountByRole(UserModel::ROLE_CUSTOMER);
    }


    protected static function getWalletBalanceForRole(string  $role)  {
        $total = 0;
        $users = static::getUsersByRole($role)->get();
        foreach ($users as $user) {
            $userObj = new \App\Koloo\User($user);
            $total += $userObj->mainWallet()->getAmount();
        }
        return  round($total / 100, 2);
    }

    public static function totalAgentsWalletBalance(): float  {
        return static::getWalletBalanceForRole(\App\User::ROLE_AGENT);
    }

    public static function totalCustomerWalletBalance(): float  {
        return static::getWalletBalanceForRole(\App\User::ROLE_CUSTOMER);
    }

}
