<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\APIBaseController;
use App\Http\Requests\ChangePasswordRequest;
use App\Koloo\User;


/**
 * Class PasswordManagement
 *
 * @package \App\Http\Controllers\Api\Admin
 */
class PasswordManagement extends APIBaseController
{

    public function store(ChangePasswordRequest $request)
    {

        $user  = User::find(request('id'));

        $user->setNewPassword(request('password'));

        return $this->successResponse();
    }
}
