<?php

namespace App\Listeners;

use App\Events\SendMessage;
use App\Koloo\User;
use App\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendFundWithdrawalNotification
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
        $amount = 'NGN'.number_format($event->amount,2);
        $customer = $event->customer;
        $agent = $event->agent;
        $balance = 'NGN' . number_format($customer->mainWallet()->getAmount(), 2);
        $channel = 'both';


        $message = sprintf(config('koloo.fund_withdrawal_notification_message'), $customer->getName(), $amount, $agent->getName(), $balance);

        $message = Message::create([
            'message' => $message,
            'message_type' => $channel,
            'user_id' => $customer->getId(),
            'sender' => User::rootUser()->getId(),
            'subject' => 'Fund withdrawal'
        ]);

        event(new SendMessage($message, $channel));
    }
}
