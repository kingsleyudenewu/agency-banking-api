<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class TransactionPinController
 *
 * @package \App\Http\Controllers\Api\Account
 */
class TransactionPinController extends APIBaseController
{

    public function store(Request $request)
    {
        $request->validate([
            'pin' => 'required|numeric|min:0',
            'password' => 'required'
            ]);

        $user = new User($request->user());

        if(!Hash::check($request->password, $user->getHashedPassword()))
        {
            return $this->errorResponse('Your password is not correct.');
        }

        $user->setTransactionPin($request->pin);

        return $this->successResponse('Transaction Pin updated');
    }
}
