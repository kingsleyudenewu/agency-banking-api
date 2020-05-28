<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Saving;
use Faker\Generator as Faker;

$factory->define(Saving::class, function (Faker $faker) {
    return [
        'amount' => 100,
        'saving_cycle_id' => factory('App\SavingCycle')->create(),
        'owner_id' => factory('App\User')->create(),
        'creator_id' => factory('App\User')->create(),
        'maturity' => now()
    ];
});
