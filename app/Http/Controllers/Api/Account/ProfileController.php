<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\APIBaseController;
use App\Http\Resources\Profile;
use App\User;

/**
 * Class ProfileController
 *
 * @package \App\Http\Controllers\Api\Account
 */
class ProfileController extends APIBaseController
{
    public function index()
    {

        $user = (request('id') && auth()->user()->hasRole('admin')) ?
                User::with('profile')->find(request('id')) : User::with('profile')->find(auth()->user()->id);

        if(!$user)
            return $this->errorResponse('User not found', null, 404);

        return $this->successResponse('OK', new Profile($user));
    }
}
