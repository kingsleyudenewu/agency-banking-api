<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Profile;
use Faker\Generator as Faker;

$factory->define(Profile::class, function (Faker $faker) {
    return [
        'user_id' => factory(\App\User::class),
        'address' => $faker->address,
        'dob' => $faker->date('Y-m-d', now()->subYears(18)),
        'gender' => $faker->randomElement(['male', 'female']),
        'bank_account_number' => $faker->numberBetween(1000000000),
        'secondary_phone' => $faker->phoneNumber,
        'next_of_kin_phone' => $faker->phoneNumber,
        'next_of_kin_name' => 'Doe',
        'marital_status' => $faker->randomElement(['married','single','unknown']),
        'lga' => 'lag',
        'business_name' => $faker->company,
        'business_address' => $faker->address,
        'business_phone' => $faker->phoneNumber,
        'bvn' => $faker->numberBetween(1000000000),
        'emergency_name' => '',
        'emergency_phone' => '',
        'commission' => 250
    ];
});
