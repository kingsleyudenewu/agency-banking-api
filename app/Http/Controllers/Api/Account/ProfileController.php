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

        if($request->input('id') && auth()->user()->hasRole(User::ROLE_ADMIN))
        {
            $user =  User::with('profile')->find($request->input('id'));
        }
        else
         {
              if ($request->input('id')){
                  $user =  User::with('profile')->where('parent_id', $request->input('id'))->first();
              } else {
                  $user =  User::with('profile')->find(auth()->user()->id);
              }
        }

        if(!$user)
            return $this->errorResponse('User not found', null, 404);

        return $this->successResponse('OK', new Profile($user));
    }
}
