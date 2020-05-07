<?php

namespace App\Listeners;

use App\Events\SendMessage;
use App\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
            'message' => 'Your Koloo OTP is: ' . $otp->code . '. Your OTP is secret and must NOT be shared with anyone else.',
            'message_type' => $event->channel,
            'user_id' => $user->getId(),
            'sender' => $user->getId(),
            'subject' => 'ACTION REQUIRED: Koloo OTP'
        ]);


       event(new SendMessage($message, $event->channel));
    }
}
