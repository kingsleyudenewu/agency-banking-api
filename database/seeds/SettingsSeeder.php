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
        settings()->set('document_storage_driver', 'public');
        settings()->set('document_storage_path', 'docs/');
        settings()->set('document_storage_max_size', 2000); // 2mb
        settings()->set('document_storage_mime_types', 'pdf,doc,docx,jpeg,jpg,png');
        settings()->set('valid_document_fields', 'passport_photograph,agreement_form');


    }

    private function clearSettings()
    {
        \Illuminate\Support\Facades\DB::table('settings')->truncate();
    }
}
