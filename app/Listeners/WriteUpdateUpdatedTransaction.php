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
        $remark = $event->remark;

        $transactionName = 'write' . ucfirst($method).'Transaction';
        $customer->$transactionName($amount,  $remark);

        $msg = $method == 'credit' ? 'You sent '  : 'You debited ';
        $authUserMethod =  $method == 'credit' ? 'writeDebitTransaction' : 'writeCreditTransaction';
        $authUser->$authUserMethod($amount, $msg .  $remark . '# ' . number_format($amount,2) . ' #' . e($customer->getName()));
    }
}
