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

        $c = \App\Country::create(['name' => 'Nigeria', 'code' => 'NG', 'currency' => 'NGN']);

        $states = ['Lagos', 'Edo', 'Enugu', 'Benue', 'Ogun'];

        foreach($states as $state)
        {
            $c->states()->create(['name' => $state]);
        }
    }
}
