<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\APIBaseController;
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

        settings()->set('max_commission', request('max_commission') * 100);
        settings()->set('min_commission', request('min_commission') * 100);
        settings()->set('percent_to_charge', request('percent_to_charge') * 100);
        settings()->set('otp_length', abs(intval(request('otp_length'))) );
        settings()->set('enable_otp_for_login', boolval(request('enable_otp_for_login')));
        settings()->set('document_storage_driver', request('document_storage_driver'));
        settings()->set('document_storage_path', request('document_storage_path'));
        settings()->set('document_storage_max_size', intval(request('document_storage_max_size'))); // Kilobyte
        settings()->set('document_storage_mime_types', request('document_storage_mime_types'));
        settings()->set('valid_document_fields', request('valid_document_fields'));

        settings()->flushCache();

        return $this->index();

    }

    public function index()
    {
        return $this->successResponse('settings', [
            'max_commission' => settings()->get('max_commission') / 100,
            'min_commission' => settings()->get('min_commission') / 100,
            'percent_to_charge' => settings()->get('percent_to_charge') / 100,
            'otp_length' => intval(settings()->get('otp_length')),
            'enable_otp_for_login' => boolval(settings()->get('enable_otp_for_login')),
            'document_storage_driver' => settings()->get('document_storage_driver'),
            'document_storage_path' => settings()->get('document_storage_path'),
            'document_storage_max_size' => settings()->get('document_storage_max_size'),
            'document_storage_mime_types' => settings()->get('document_storage_mime_types'),
            'valid_document_fields' => settings()->get('valid_document_fields')
        ]);
    }



}
