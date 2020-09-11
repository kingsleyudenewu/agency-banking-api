<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\APIBaseController;
use App\Http\Requests\BalanceCreditAndDebitRequest;
use App\Koloo\User;
use App\Traits\LogTrait;
use App\Transaction;

/**
 * Class CreditAndDebitController
 *
 * @package \App\Http\Controllers\Api\Admin
 */
class CreditAndDebitController extends APIBaseController
{
    use LogTrait;

    const ACTION_CREDIT = 'credit';
    const SOURCE_WALLET = 'wallet';

    public function store(BalanceCreditAndDebitRequest $request)
    {

        try {
            $authUser = new User($request->user());

            $user = User::find($request->input('user_id'));
            User::checkExistence($user);

            $source = $request->input('source') === static::SOURCE_WALLET ? $user->mainWallet() : $user->purse();
            $amount = $request->input('amount');
            $action = $request->input('action');
            $remark = $request->input('remark') ?: 'manual ' . $action . ' transaction';

            $remark = '['. $request->input('source').'] ' . $remark;


            if($action === static::ACTION_CREDIT)
            {
                $user->creditWalletSource($amount, $source, $remark, Transaction::LABEL_MANUAL);
            }
            else
            {
                $user->chargeWalletSource($amount, $source, $remark, Transaction::LABEL_MANUAL);
            }


            $this->logInfo('Manual Transaction: Performed by: ' . $authUser->getName() . ' for: ' . $user->getName() . ' amount: N' . $amount);

            return $this->successResponse('Operation successful.');

        } catch (\Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
}
