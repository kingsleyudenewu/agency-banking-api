<?php

namespace App\Listeners;

use App\Events\SendMessage;
use App\Message;

class HandleNewOTP
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
        $otp = $event->otp;
        $user = $event->user;

        $message = Message::create([
            'message' => sprintf(config('koloo.otp_message'), $otp->code),
            'message_type' => $event->channel,
            'user_id' => $user->getId(),
            'sender' => $user->getId(),
            'subject' => 'ACTION REQUIRED: Koloo OTP'
        ]);


       event(new SendMessage($message, $event->channel));
    }
}
