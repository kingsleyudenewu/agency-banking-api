<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Profile;
use Faker\Generator as Faker;

$factory->define(Profile::class, function (Faker $faker) {
    return [
        'user_id' => factory(\App\User::class),
        'address' => $faker->address,
        'passport' => 'passport.png',
        'dob' => $faker->date('Y-m-d', now()->subYears(18)),
        'gender' => $faker->randomElement(['male', 'female']),
        'bank_account_number' => $faker->numberBetween(1000000000),
        'bank_name' => $faker->randomElement(['GTBank', 'Access Bank', 'UBA', 'Fidelity', 'FCM']),
        'secondary_phone' => $faker->phoneNumber,
        'next_of_kin_phone' => $faker->phoneNumber,
        'marital_status' => $faker->randomElement(['married','single','unknown']),
        'lga' => 'lag',
        'state_id' => factory(\App\State::class),
        'business_name' => $faker->company,
        'business_address' => $faker->address,
        'business_phone' => $faker->phoneNumber,
        'bvn' => $faker->numberBetween(1000000000),
    ];
});
