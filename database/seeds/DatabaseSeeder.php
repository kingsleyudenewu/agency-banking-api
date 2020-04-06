<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call(LaravelEntrustSeeder::class);
        $this->call(CountrySeeder::class);
    }
}
