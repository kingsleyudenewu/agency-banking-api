<?php

namespace App\Listeners;

use App\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleWalletBilled implements ShouldQueue
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
        $event->wallet->getOwner()->writeTransaction($event->amount, Transaction::TRANSACTION_TYPE_DEBIT,  $event->reason, $event->label);
    }
}
