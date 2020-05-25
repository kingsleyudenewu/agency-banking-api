<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class WriteUpdateUpdatedTransaction
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $method = $event->transType;
        $amount = $event->amount;
        $authUser = $event->performedBy;
        $customer  = $event->toAccount;

        $transactionName = 'write' . ucfirst($method).'Transaction';
        $customer->$transactionName($amount, 'Account credited. #' . e($authUser->getName() . '. New balance is ' . number_format($customer->mainWallet()->getAmount(), 2)));

        $msg = $method == 'credit' ? 'You sent '  : 'You debited ';
        $authUser->writeDebitTransaction($amount, $msg .  number_format($amount,2) . ' #' . e($customer->getName()));
    }
}
