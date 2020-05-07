<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\APIBaseController;
use App\Http\Resources\Profile;

/**
 * Class ProfileController
 *
 * @package \App\Http\Controllers\Api\Account
 */
class ProfileController extends APIBaseController
{
    public function index()
    {
        return $this->successResponse('OK', new Profile(auth()->user()));
    }
}
