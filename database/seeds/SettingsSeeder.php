<?php

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->clearSettings();

        settings()->set('max_commission', 2.5);
        settings()->set('currency_precision', 2);

    }

    private function clearSettings()
    {
        \Illuminate\Support\Facades\DB::table('settings')->truncate();
    }
}
