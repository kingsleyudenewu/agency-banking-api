<?php

namespace App\Http\Controllers\Api\Service;

use App\Events\SendMessage;
use App\Http\Controllers\APIBaseController;
use App\Koloo\User;
use App\Message;
use App\ProvidusTransaction;
use App\Services\Monnify\Api;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


/**
 * Class MonnifyController
 *
 * @package \App\Http\Controllers\Api\Service
 */
class MonnifyController extends APIBaseController
{

    public function check(Request $request, Api $monnifyApi)
    {
        $logger = Log::channel('monnify-account-topup');


        $logger->info('Monnify webhook. Verifying a hash.', $request->all());

        $check = ProvidusTransaction::where('ref', $request->get('transactionReference'))->first();
        if($check && $check->isCompleted())
        {
            $msg = 'ProvidusTransaction not found or already treated.';
            $logger->error($msg .  $check);
            return $this->errorResponse($msg);
        }

        try {
            $monnifyApi->verifyWebhook($request->all());
        }
        catch(\Exception $exception) {
            $logger->error('PAYMENT VERIFICATION FAILED.' . $exception->getMessage(), $request->all());
            return $this->errorResponse($exception->getMessage());
        }

        $amountToCredit = 0;

        try {

            $payment = $monnifyApi->getSuccessfulTransaction($request->get('transactionReference'));

            if (! $user = User::findByProvidusReference($request->get('product')['reference'])) {
                $logger->error('Monnify webhook. User account reference is not found. ', $request->all());
                return $this->errorResponse('User not found.', null, 400);
            }

            if($user->isCustomer())
            {
                $amountToCredit = $this->applyCharges($payment->payableAmount);
            }
            else
            {
                $amountToCredit = $payment->payableAmount;
            }


            $user->mainWallet()->credit($amountToCredit);

            $user->writeCreditTransaction($amountToCredit, sprintf('%s%s was deposited into your account via providus bank tranfer', $payment->currencyCode, number_format($payment->payableAmount, 2)), Transaction::LABEL_MONNIFY);

            ProvidusTransaction::create(['ref' => $payment->transactionReference, 'payload' => json_encode($payment), 'completed' => now()]);

            $amountInfo = sprintf(' %s %s', $payment->currencyCode, number_format($payment->payableAmount, 2)) . ' via Providus bank';

            $message = Message::create([
                'message' => sprintf(config('koloo.account_funded_message'), $amountInfo),
                'message_type' => 'sms',
                'user_id' => $user->getId(),
                'sender' => $user->getId(),
                'subject' => ''
            ]);


            event(new SendMessage($message, 'sms'));

            return $this->successResponse('Funded.');

        } catch(\Exception $exception) {

            $user->mainWallet()->debit($amountToCredit);
            $user->writeDebitTransaction($amountToCredit, 'Revered due to error: ' . $exception->getMessage(), 'Monnify');

            $logger->error($exception->getMessage() . ' File ' . $exception->getFile() . ' on line ' . $exception->getLine() . ' Code ' . $exception->getCode());
            $logger->error($request->all());
            return $this->errorResponse($exception->getMessage());
        }



    }

    private function applyCharges($payableAmount)
    {
        $charge10kBelow = settings('transfer_charge_10k_below') ? settings('transfer_charge_10k_below') / 100 : 0;
        $charge10kAbove = settings('transfer_charge_above_10k') ? settings('transfer_charge_above_10k') / 100 : 0;

        $newValue = $payableAmount;
        $amountCharged = 0;
        // If the user is paying about 10k
        if($payableAmount <= 10000)
        {
            $amountCharged = $charge10kBelow;
            $newValue = $newValue - $charge10kBelow;
        } else {
            $amountCharged = $charge10kAbove;
            $newValue = $newValue - $charge10kAbove;
        }

        if($newValue !== $payableAmount && $charge10kAbove !== 0)
        {
            // Some charges has been applied
            $rootUser = User::rootUser();
            $rootUser->creditWalletSource($amountCharged, $rootUser->mainWallet(), 'Monnify charge', Transaction::LABEL_MONNIFY);
        }

        return $newValue;

    }

}
