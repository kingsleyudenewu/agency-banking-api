<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\APIBaseController;
use App\Saving;

/**
 * Class AdminViewSavingsController
 *
 * @package \App\Http\Controllers\Api\Admin
 */
class AdminViewSavingsController extends APIBaseController
{

    public function index()
    {
        return Saving::with(['creator:id,name', 'owner:id,name'])->get();
    }

}
