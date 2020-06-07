<?php

namespace App\Listeners;

use App\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleFundTransfer
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
        $event->customer->writeTransaction($event->amount, Transaction::TRANSACTION_TYPE_CREDIT,  $event->remark, Transaction::LABEL_TRANSFER);
    }
}
