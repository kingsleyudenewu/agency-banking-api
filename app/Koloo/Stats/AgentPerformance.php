<?php

namespace App\Koloo\Stats;

use App\AgentPerformanceStat;


/**
 * Class AgentPetformace
 *
 * @package \App\Koloo\Stats
 */
class AgentPerformance {

        public static function getUserSavingVolume(\App\User $user)
        {
            return $user->contributions()->count();
        }

        public static function getUserSavingValue(\App\User $user)
        {
            return $user->contributions()->sum('amount');
        }

        public static function getCustomerAcquired(\App\User $user)
        {
            return $user->children()->count();

        }

        public static function populateTable()
        {
            AgentPerformanceStat::truncate();

            $collections = User::getUsersByRole(\App\User::ROLE_SUPER_AGENT)->get()
                           ->merge(User::getUsersByRole(\App\User::ROLE_AGENT)->get());


            foreach($collections as $user)
            {

                $savingVolume  = static::getUserSavingVolume($user);
                if($savingVolume > 0)
                {
                    $savingValue = static::getUserSavingValue($user);
                    $customerAcquired = static::getCustomerAcquired($user);

                    AgentPerformanceStat::create([
                        'user_id' =>  $user->id,
                        'saving_volume' =>  $savingVolume,
                        'saving_value' => $savingValue / 100,
                        'customer_acquired' => $customerAcquired
                    ]);
                }
            }

        }
}
