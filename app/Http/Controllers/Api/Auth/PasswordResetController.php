<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\NewPasswordRequested;
use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use Illuminate\Http\Request;

/**
 * Class PasswordResetController
 *
 * @package \App\Http\Controllers\Api\Auth
 */
class PasswordResetController extends APIBaseController
{


    public function store(Request $request)
    {
        $request->validate(['identity' => 'required']);

        try {
            $user = User::findByIdentity($request->input('identity'), $request->input('country'));
            User::checkExistence($user);

            event(new NewPasswordRequested($user, 'web'));

            return $this->successResponse('Check your device/email for a new instruction.');


        } catch (\Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
}
