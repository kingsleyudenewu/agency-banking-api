<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Transaction;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'user_id' => factory('App\User'),
        'type' => 'credit',
        'amount' => 1000,
        'trans_ref' => str_random(35),
        'remark' => 'test'
    ];
});
