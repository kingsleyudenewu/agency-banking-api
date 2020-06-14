<?php

namespace App\Http\Controllers\Api\Account;

use App\Events\CustomerFundWithdrawal;
use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use App\Traits\LogTrait;
use App\Transaction;
use Illuminate\Http\Request;

/**
 * Class WithdrawalController
 *
 * @package \App\Http\Controllers\Api\Account
 */
class WithdrawalController extends APIBaseController
{
    use LogTrait;

    public function store(Request $request)
    {
        $this->logChannel = 'Withdrawal';

        $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'amount' => 'required'
        ]);



        try {
            $customer = User::find($request->input('user_id'));
            if(!$customer->isCustomer())
            {
                throw new \Exception('Withdrawal not permitted for this account. If you are an agent/super-agent, please use the payout option.');
            }
           User::otpRequiredToContinue($request, $customer);

            $authUser = new User($request->user());
            if($authUser->isCustomer()) throw new \Exception('Action not allowed');

            $amount = doubleval(str_replace(',', '', trim($request->input('amount'))));

            $agentPurse = $authUser->purse();
            $customerWallet = $customer->mainWallet();

            $this->logInfo('Charging the user ' . $customer->getId() . ' the amount: ' . $amount);
            $this->logInfo('Will credit ' . $authUser->getId() . ' the amount ' . $amount);


            $customer->chargeWalletSource($amount, $customerWallet, 'Withdrawal via agent ' . $authUser->getName(), Transaction::LABEL_WITHDRAWAL);

            $customer->creditWalletSource($amount, $agentPurse, 'Withdrawal for ' . $customer->getName(), Transaction::LABEL_WITHDRAWAL);

            event(new CustomerFundWithdrawal($amount, $customer, $authUser));

            return $this->successResponse('Transaction successful. You can now pay cash to the customer.');

        } catch (\Exception $e)
        {
            return response(['message' => $e->getMessage(), 'otp_required' => true], 401);
        }



    }
}
