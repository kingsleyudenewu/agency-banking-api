<?php

namespace App\Http\Controllers\Api\Customer;

use App\Events\UserAccountPasswordUpdated;
use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use Illuminate\Http\Request;

/**
 * Class SetPasswordController
 *
 * @package \App\Http\Controllers\Api\Customer
 */
class SetPasswordController extends APIBaseController
{

    public function store(Request $request)
    {
        try {
            $user = User::findByEmail($request->input('email'));
            User::checkExistence($user);

            $check = $user->passwordResetValid($request->input('code'));
            if(!$check) throw new \Exception('Your password reset code is not valid.');

            if(!$user->setNewPassword($request->input('password')))
                throw new \Exception('Unable to set new password');

            event(new UserAccountPasswordUpdated($user, $request->input('password')));

            $user->clearResetPassword();

            return $this->successResponse('Password updated.');

        }
        catch (\Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

}
