<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\APIBaseController;
use App\Http\Resources\Profile;
use App\User;
use Illuminate\Http\Request;

/**
 * Class ProfileController
 *
 * @package \App\Http\Controllers\Api\Account
 */
class ProfileController extends APIBaseController
{
    public function index(Request $request)
    {
        $user = null;

        if($request->input('id'))
        {
            if(auth()->user()->hasRole(User::ROLE_ADMIN))
            {
                $user =  User::with('profile')->find($request->input('id'));
            } else {
                $user =  User::with('profile')
                    ->where('parent_id', auth()->user()->id)
                    ->find( $request->input('id'));
            }
        }


        if(!$request->input('id') && !$user)
        {
            $user =  User::with('profile')->find(auth()->user()->id);
        }

        if(!$user)
            return $this->errorResponse('User not found', null, 404);

        return $this->successResponse('OK', new Profile($user));
    }
}
