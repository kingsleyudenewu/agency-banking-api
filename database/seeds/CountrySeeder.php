<?php

use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Country::truncate();

        \App\Country::create(['name' => 'Nigeria', 'code' => 'NG', 'currency' => 'NGN']);
    }
}
