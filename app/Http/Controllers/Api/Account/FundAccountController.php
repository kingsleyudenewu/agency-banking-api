<?php

namespace App\Http\Controllers\Api\Account;

use App\Events\FundTransfer;
use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class FundAccountController
 *
 * @package \App\Http\Controllers\Api\Account
 */
class FundAccountController extends APIBaseController
{

    public function fund(Request $request)
    {
        $performedBy = new User($request->user());

        if($performedBy->isAdmin())
            $performedBy  = User::rootUser();

        $amount = $request->input('amount');
        $remark = $request->input('remark');

        try {

            $customer = User::find($request->input('user_id'));
            User::checkExistence($customer);

            if(!$customer->mainWallet()->isValid()){
                throw new \Exception('The customer wallet is not in a valid state.');
            }

            $performedBy->chargeWallet($amount, $remark, Transaction::LABEL_TRANSFER);

            $customer->mainWallet()->credit($amount);

            event(new FundTransfer($performedBy, $customer, $amount, $remark));

            return $this->successResponse('Successful', ['balance' => $performedBy->mainWallet()->getAmount()]);

        } catch (\Exception $e)
        {
            Log::error('BalanceController :: ' . $e->getMessage());

            return $this->errorResponse($e->getMessage());
        }
    }
}
