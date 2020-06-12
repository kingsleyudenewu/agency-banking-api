<?php

namespace App\Http\Controllers\Api\Account;

use App\CommissionPayout;
use App\Events\PayoutRequested;
use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use App\Traits\LogTrait;
use App\Transaction;
use Illuminate\Http\Request;

/**
 * Class CommissionPayoutRequest
 *
 * @package \App\Http\Controllers\Api\Account
 */
class CommissionPayoutRequest extends APIBaseController
{
    use LogTrait;


    public function index(Request $request)
    {
        $customer = User::findByInstance($request->user());

        $query = CommissionPayout::query();

        if(!$customer->isAdmin())
            $query = $query->where('user_id', $customer->getId());


        $res = $query->with(['user:id,name', 'user.wallets'])
                ->latest()
                ->paginate($this->perginationPerPage());
        return $this->successResponse('payouts', $res);

    }


    public function store(Request $request)
    {
        $this->logChannel = 'Payout';

        $request->validate(['amount' => 'required']);

        try {

            $amount = doubleval(str_replace(',', '', trim($request->input('amount'))));

            $customer = User::findByInstance($request->user());
            User::checkExistence($customer);

            $customer->checkPendingRequest();

            $source = $customer->purse();

            $this->logInfo('Charging the user ' . $customer->getId() . ' the amount: ' . $amount);

            $customer->chargeWalletSource($amount, $source, 'Commission payout', Transaction::LABEL_PAYOUT);

            $payout = CommissionPayout::create([
                'user_id' => $customer->getId(),
                'wallet_id' => $source->getId(),
                'amount' => $amount,
            ]);

            $this->log($payout);

            event(new PayoutRequested($payout));

            return $this->successResponse('Payout created', $payout);

        } catch (\Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

}
