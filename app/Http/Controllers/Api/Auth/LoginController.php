<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\APIBaseController;
use App\Http\Requests\LoginRequest;
use App\Koloo\User;
use App\Koloo\PhoneNumber;
use Illuminate\Support\Facades\Hash;
/**
 * Class LoginController
 *
 * @package \App\Http\Api\Auth
 */
class LoginController extends APIBaseController
{
    public function postLogin(LoginRequest $request)
    {
        try {

            $user = User::findByIdentity($request->input('identity'), $request->input('country'));

            if (! $user OR ! Hash::check($request->password, $user->getHashedPassword())) {
                throw new \Exception('Login and/or password are incorrect.');
            }

            if(!$user->isAdmin() && !$user->isApproved()) throw new \Exception('Your account has not been approved');

            $user = $user->isAdmin() ? User::rootUser() : $user;

            $user->updateLastLogin();

            $user->newAPIToken()
                ->determineLoginOTP();

            auth()->setUser($user->getModel());
            return $this->successResponseWithUser('OK', $user->getLoginResponse());

        } catch (\Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }

    }


}
