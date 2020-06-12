<?php

namespace App\Http\Controllers\Api\Account;

use App\CommissionPayout;
use App\Events\CommissionPayoutStatusChanged;
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

    const ACTION_APPROVE = 'approve';
    const ACTION_PAID = 'paid';

    const SEARCH_STATUS_PENDING = 'pending';
    const SEARCH_STATUS_AWAITING_PAYMENT = 'awaiting';
    const SEARCH_STATUS_PAID = 'paid';


    public function index(Request $request)
    {
        $customer = User::findByInstance($request->user());

        $query = CommissionPayout::query();

        if(!$customer->isAdmin())
            $query = $query->where('user_id', $customer->getId());

        if($request->input('q'))
        {
            $search = strtolower(trim($request->input('q')));
            if($search === static::SEARCH_STATUS_AWAITING_PAYMENT)
                $query->where('status', CommissionPayout::STATUS_WAITING_PAYMENT);
            else if($search === static::SEARCH_STATUS_PAID)
                $query->where('status', CommissionPayout::STATUS_PAID);
            else if($search === static::SEARCH_STATUS_PENDING)
                $query->where('status', CommissionPayout::STATUS_PENDING);
        }


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


    public function update(Request $request, $id)
    {
        $authUser = User::findByInstance($request->user());

        try {
            if(!$authUser->isAdmin()) throw new \Exception('Access denied');

            $payout = CommissionPayout::find($id);
            $action = strtolower(trim(request('action')));

            if($payout->paid) throw new \Exception('You can not update a paid request.');

            switch ($action) {
                case static::ACTION_APPROVE:
                    $payout->updateStatus(CommissionPayout::STATUS_WAITING_PAYMENT);
                    break;
                case static::ACTION_PAID:
                    $payout->markAsPaid($authUser->getId());
                    break;
                default:
                    throw new \Exception('Invalid action');
            }

            event(new CommissionPayoutStatusChanged($payout));

            return $this->successResponse('Updated', $payout);

        } catch (\Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

}
