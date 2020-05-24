<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SavingCycle;
use Faker\Generator as Faker;

$factory->define(SavingCycle::class, function (Faker $faker) {
    return [
        'title' => $faker->text,
        'duration' => 30,
        'description' => $faker->text,
        'min_saving_frequent' => 60,
        'charge_type' => 'flat',
        'min_frequent_saving' => 1000 * 100
    ];
});
