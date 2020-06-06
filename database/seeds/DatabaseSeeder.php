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
        \App\Wallet::truncate();
        \App\Transaction::truncate();
        \App\Message::truncate();

        $this->call(LaravelEntrustSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(BankSeeder::class);
        $this->call(RootUserSeeder::class);

    }
}
