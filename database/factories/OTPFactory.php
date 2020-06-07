<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\OTP;
use Faker\Generator as Faker;

$factory->define(OTP::class, function (Faker $faker) {
    return [
        'phone' => $faker->phoneNumber,
        'code' => $faker->numberBetween(1000, 99999999),
        'expire_at' => now()->addMinutes(60)
    ];
});
