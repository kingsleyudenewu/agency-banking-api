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

        $phone =  PhoneNumber::format($request->identity, $request->country);


        /** @var User $user */
        $user = User::findByPhone($phone);

        if (! $user OR ! Hash::check($request->password, $user->getHashedPassword())) {
            return $this->errorResponse('Login and/or password are incorrect.');
        }

        $user->newAPIToken()
            ->determineLoginOTP();


        auth()->setUser($user->getModel());
        return $this->successResponseWithUser('OK', $user->getLoginResponse());

    }


}
