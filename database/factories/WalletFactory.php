<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Wallet;
use Faker\Generator as Faker;

$factory->define(Wallet::class, function (Faker $faker) {
    return [
        'user_id' => factory('App\User')->create(),
        'currency' => 'NGN',
        'hash' => $faker->randomNumber(),
        'amount' => 0,
        'type' => 'wallet'
    ];
});
