<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use Illuminate\Http\Request;

/**
 * Class SettingsController
 *
 * @package \App\Http\Controllers\Api\Admin
 */
class SettingsController extends APIBaseController
{

    public function store(Request $request)
    {


        // Update root user commission
        $rootUser = User::rootUser();

        $rootUser->setCommission(request('min_commission') * 100);


        settings()->set('min_commission', request('min_commission') * 100); // This is used for the system
        settings()->set('percent_to_charge', request('percent_to_charge') * 100);
        settings()->set('otp_length', abs(intval(request('otp_length'))) );
        settings()->set('enable_otp_for_login', boolval(request('enable_otp_for_login')));
        settings()->set('document_storage_driver', request('document_storage_driver'));
        settings()->set('document_storage_path', request('document_storage_path'));
        settings()->set('document_storage_max_size', intval(request('document_storage_max_size'))); // Kilobyte
        settings()->set('document_storage_mime_types', request('document_storage_mime_types'));
        settings()->set('valid_document_fields', request('valid_document_fields'));
        settings()->set('password_reset_validity_days', request('password_reset_validity_days'));
        settings()->set('frontend_password_reset_base_url', request('frontend_password_reset_base_url'));
        settings()->set('withdrawal_charge', request('withdrawal_charge') * 100);




        settings()->flushCache();

        return $this->index();

    }

    public function index()
    {
        return $this->successResponse('settings', [
            'min_commission' => settings()->get('min_commission') / 100,
            'percent_to_charge' => settings()->get('percent_to_charge') / 100,
            'otp_length' => intval(settings()->get('otp_length')),
            'enable_otp_for_login' => boolval(settings()->get('enable_otp_for_login')),
            'document_storage_driver' => settings()->get('document_storage_driver'),
            'document_storage_path' => settings()->get('document_storage_path'),
            'document_storage_max_size' => settings()->get('document_storage_max_size'),
            'document_storage_mime_types' => settings()->get('document_storage_mime_types'),
            'valid_document_fields' => settings()->get('valid_document_fields'),
            'password_reset_validity_days' => intval(settings()->get('password_reset_validity_days')),
            'frontend_password_reset_base_url' => settings()->get('frontend_password_reset_base_url'),
            'withdrawal_charge' => settings()->get('withdrawal_charge') / 100,
        ]);
    }



}
