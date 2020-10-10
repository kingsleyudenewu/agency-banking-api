<?php

namespace App\Listeners;

use App\CommissionPayout;
use App\Events\SendMessage;
use App\Koloo\User;
use App\Message;
use App\Traits\LogTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleCommissionPayoutStatusChanged implements ShouldQueue
{
    use LogTrait;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->logChannel = 'Payout';
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $payout = $event->payout;
        $customer = new User($payout->user);

        if(!$customer) throw new \Exception('Customer not set for this payout object.' .  $payout->id);

        $message = '';
        $amount = 'NGN' . number_format($payout->amount,2);
        $channel = 'both';

        if($payout->status === CommissionPayout::STATUS_PAID)
        {
            $message = sprintf(config('koloo.commission_payout_request_paid_message'), $amount);
        } else if($payout->status === CommissionPayout::STATUS_WAITING_PAYMENT)
        {
            $message = sprintf(config('koloo.commission_payout_request_approved_message'), $amount);
        }

        if($message)
        {
            $message = Message::create([
                'message' => $message,
                'message_type' => $channel,
                'user_id' => $customer->getId(),
                'sender' => User::rootUser()->getId(),
                'subject' => 'Notification'
            ]);

            event(new SendMessage($message, $channel));
        }


    }
}
