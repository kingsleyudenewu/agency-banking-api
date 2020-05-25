<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Contribution;
use Faker\Generator as Faker;

$factory->define(Contribution::class, function (Faker $faker) {
    return [
        'amount' => 10000,
        'created_by' => factory('App\User')->create(),
        'saving_id' => factory('App\Saving')
    ];
});
