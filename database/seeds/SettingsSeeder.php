<?php

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    //9000.0
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->clearSettings();

        settings()->set('max_commission', 10000); // 100%
        settings()->set('min_commission', 2000); // this is reserved for the system
        settings()->set('percent_to_charge', 330); // How much to charge per savings- This is the system-wide charge
        settings()->set('currency_precision', 2);
        settings()->set('otp_length', 8);
        settings()->set('enable_otp_for_login', false);
        settings()->set('document_storage_driver', 'do_spaces');
        settings()->set('document_storage_path', 'docs/');
        settings()->set('document_storage_max_size', 2000); // 2mb
        settings()->set('document_storage_mime_types', 'pdf,doc,docx,jpeg,jpg,png');
        settings()->set('valid_document_fields', 'means_of_identification,agreement_form,application_form');
        settings()->set('password_reset_validity_days', 5);
        settings()->set('frontend_password_reset_base_url', 'http://frontend-v1-3.now.sh/onboarding/get-started');
        settings()->set('withdrawal_charge', 10000); // 100 Naira
        settings()->set('withdrawal_charge_for_agent', 5000); // 50%
        settings()->set('transaction_auth', 'pin'); // pin or otp
        settings()->set('transfer_charge_10k_below', 100 * 100);
        settings()->set('transfer_charge_above_10k', 200 * 100);



    }

    private function clearSettings()
    {
        \Illuminate\Support\Facades\DB::table('settings')->truncate();
    }
}
