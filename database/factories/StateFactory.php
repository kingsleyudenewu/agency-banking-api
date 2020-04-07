<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\State;
use Faker\Generator as Faker;

$factory->define(State::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'country_id' => factory(\App\Country::class)
    ];
});
