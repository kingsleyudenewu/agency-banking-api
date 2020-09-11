<?php

namespace App\Listeners;

use App\Events\SendMessage;
use App\Koloo\User;
use App\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSavingSweptNotification implements ShouldQueue
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
     * @param object $event
     *
     * @return void
     * @throws \Exception
     */
    public function handle($event)
    {
        $owner = new User($event->saving->owner);

        //
        $channel = 'sms';
        $balance = 'N' . number_format($owner->mainWallet()->getAmount());

        $message = Message::create([
            'message' => sprintf(config('koloo.saving_swept_message'), $balance),
            'message_type' => $channel,
            'user_id' => $owner->getId(),
            'sender' => User::rootUser()->getId(),
            'subject' => 'Your savings is mature'
        ]);

        event(new SendMessage($message, $channel));
    }
}
