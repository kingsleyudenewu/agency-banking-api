<?php

namespace App\Http\Controllers\Api\Account;

use App\Message;
use App\CommissionPayout;
use App\Events\SendMessage;
use App\Events\PayoutRequested;
use App\Http\Controllers\APIBaseController;
use App\Events\CommissionPayoutStatusChanged;
use App\Koloo\User;
use App\Traits\LogTrait;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    const SOURCE_TO_CREDIT = 'wallet';


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


        $res = $query->with(['user:id,name', 'user.wallets', 'bank:id,name'])
                ->latest()
                ->paginate($this->perginationPerPage());
        return $this->successResponse('payouts', $res);

    }


    public function store(Request $request)
    {

        $rules = [
            'amount' => 'required',
        ];


        $creditWallet =  (bool)strtolower($request->input('source') === static::SOURCE_TO_CREDIT);

        if(!$creditWallet) {
            $rules['bank_id'] = 'required|uuid|exists:banks,id';
            $rules['bank_account_number'] = 'required';
            $rules['bank_account_name'] = 'required';
        }

        $request->validate($rules);


        try {

            DB::beginTransaction();

            $customer = User::findByInstance($request->user());
            User::checkExistence($customer);


            $amount = doubleval(str_replace(',', '', trim($request->input('amount'))));

            $customer = User::findByInstance($request->user());
            User::checkExistence($customer);

            $customer->checkPendingRequest();

            $source = $customer->purse();

            $this->logInfo('Commission Payout Request:  Charging the user ' . $customer->getId() . ' the amount: ' . $amount);
            $labelInfo = 'Commission payout';

            $customer->chargeWalletSource($amount, $source, $labelInfo, Transaction::LABEL_PAYOUT);


            if($creditWallet) {
                $customer->creditWalletSource($amount, $customer->mainWallet(), $labelInfo, Transaction::LABEL_PAYOUT);
                $this->logInfo("Commission Payout Request: PAYOUT TO WALLET. AMOUNT: " .  $amount . " USER: " . $customer->getName() . " USER ID: " . $customer->getId() );

            } else {
                $payout = CommissionPayout::create([
                    'user_id' => $customer->getId(),
                    'wallet_id' => $source->getId(),
                    'amount' => $amount,
                    'bank_id' => $request->input('bank_id'),
                    'bank_account_number' => $request->input('bank_account_number'),
                    'bank_account_name' => $request->input('bank_account_name')
                ]);
                
                $infoMessage = "Commission Payout Request: PAYOUT VIA BANK. AMOUNT: " .  $amount . " USER: " . $customer->getName() . " USER ID: " . $customer->getId();
                
                //Construct admin payout request notification message
                $rootUser = User::rootUser();
                $payoutMessage = Message::create([
                    'message' => $infoMessage,
                    'message_type' => 'email',
                    'user_id' => $rootUser->getId(),
                    'sender' => $rootUser->getId(),
                    'subject' => 'New Payment Request ' . now()
                ]);

                event(new PayoutRequested($payout));
                event(new SendMessage($payoutMessage, 'email'));

                $this->logInfo($infoMessage);
            }

            DB::commit();

            return $this->successResponse('Payout created', ['status' => 'ok']);

        } catch (\Exception $e)
        {
            DB::rollBack();
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
