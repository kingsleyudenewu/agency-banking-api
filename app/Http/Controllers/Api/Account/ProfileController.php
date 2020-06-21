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

        if($request->input('id') && auth()->user()->hasRole('admin'))
        {
            $user =  User::with('profile')->find($request->input('id'));
        } else {
            $user =  User::with('profile')
                ->where('id',auth()->user()->id)
                ->orWhere('parent_id', auth()->user()->id)->first();
        }


        if(!$user)
            return $this->errorResponse('User not found', null, 404);

        return $this->successResponse('OK', new Profile($user));
    }
}
