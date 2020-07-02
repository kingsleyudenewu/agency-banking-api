<?php

namespace App\Http\Controllers\Api\Account;

use App\Events\CustomerFundWithdrawal;
use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use App\Traits\LogTrait;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'amount' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $customer = User::find($request->input('user_id'));

            if(!$customer->isCustomer())
            {
                throw new \Exception('Withdrawal not permitted for this account. If you are an agent/super-agent, please use the payout option.');
            }

            try {
                User::otpRequiredToContinue($request, $customer);
            } catch (\Exception $e)
            {
                return response(['message' => $e->getMessage(), 'otp_required' => true], 401);
            }


            $authUser = new User($request->user());
            if($authUser->isCustomer()) throw new \Exception('Action not allowed');

            $withdrawalCharge = settings('withdrawal_charge') / 100;
            $withdrawalChargeForAgent = settings('withdrawal_charge_for_agent') / 100;

            $amount = doubleval(str_replace(',', '', trim($request->input('amount'))));
            $totalCharge = $withdrawalCharge + $amount;

            $agentPurse = $authUser->purse();
            $customerWallet = $customer->mainWallet();

            $customer->canChargeWallet($totalCharge);

            $this->logInfo(sprintf('Customer: %s::%s is withdrawing %s, charges: %s: Agent name: %s, total to charge customer is: %s',
                $customer->getId(), $customer->getName(), number_format($amount,2), number_format($withdrawalCharge,2), $authUser->getName(), number_format($totalCharge,2)  ));

            $customer->chargeWalletSource($amount, $customerWallet, 'Withdrawal via agent ' . $authUser->getName(), Transaction::LABEL_WITHDRAWAL);

            if($withdrawalCharge > 0)
            {
                $customer->chargeWalletSource($withdrawalCharge, $customerWallet, 'Withdrawal charge via ' . $authUser->getName(), Transaction::LABEL_WITHDRAWAL);
            }


            $authUser->creditWalletSource($amount, $agentPurse, 'Withdrawal for ' . $customer->getName(), Transaction::LABEL_WITHDRAWAL);

            $systemEarning = $withdrawalCharge;

            if($withdrawalCharge > 0 && $withdrawalChargeForAgent > 0 && !$authUser->isAdmin())
            {
                $agentEarn  = percentOf($systemEarning, $withdrawalChargeForAgent);
                $systemEarning -= $agentEarn;
                $authUser->creditWalletSource($agentEarn, $agentPurse, 'Withdrawal commission for ' . $customer->getName(), Transaction::LABEL_WITHDRAWAL);
            }

            if($withdrawalCharge > 0 )
            {
                $rootUser = User::rootUser();
                $rootUser->creditWalletSource($systemEarning, $rootUser->purse(), 'Withdrawal commission for ' . $customer->getName(), Transaction::LABEL_WITHDRAWAL);
            }

            event(new CustomerFundWithdrawal($amount, $customer, $authUser));

            DB::commit();

            return $this->successResponse('Transaction successful. You can now pay cash to the customer NGN' . number_format($amount));

        } catch (\Exception $e)
        {
            DB::rollBack();
            return response(['message' => $e->getMessage(), 'otp_required' => true], 401);
        }



    }
}
