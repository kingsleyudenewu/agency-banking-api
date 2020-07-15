<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use Illuminate\Http\Request;

/**
 * Class TransactionPinController
 *
 * @package \App\Http\Controllers\Api\Account
 */
class TransactionPinController extends APIBaseController
{

    public function store(Request $request)
    {
        $request->validate(['pin' => 'required|numeric|min:0']);

        $user = new User($request->user());

        $user->setTransactionPin($request->pin);

        return $this->successResponse('Transaction Pin updated');
    }
}
