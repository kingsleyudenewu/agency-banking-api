<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\APIBaseController;
use App\Http\Requests\OTPValidationRequest;
use App\Koloo\OtpVerification;
use App\Koloo\User;

/**
 * Class LoginOTPController
 *
 * @package \App\Http\Controllers\Api\Auth
 */
class LoginOTPController extends APIBaseController
{


    public function process(OTPValidationRequest $request)
    {
        $user = User::find($request->get('id'));

        if(!$user) return $this->errorResponse('User not found');

        $otp = new OtpVerification($user);

        if($otp->isValid(request('code')))
        {
            auth()->setUser($user->getModel());

            $res =  $user->getLoginResponse($otp->getLastOtp());

            $otp->invalidateActiveOtp();
            return $this->successResponseWithUser('OK', $res);
        }

        return $this->errorResponse('OTP not valid/expired.');
    }
}
