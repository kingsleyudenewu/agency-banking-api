<?php

namespace App\Listeners;

use App\Events\SendMessage;
use App\Koloo\User;
use App\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySuperAgentOnAccountDisapproval
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
        $user = $event->user->getParent();

        if(!$user)
        {
            $this->logChannel = 'NotifySuperAgentOnAccountDisapproval';
            $this->logInfo($event->user->getName() . ' has no parent');
            return;
        }

        $channel = 'sms';

        $message = Message::create([
            'message' => sprintf(config('koloo.agent_account_disapproved_message'), $user->getName(), $event->remark),
            'message_type' => $channel,
            'user_id' => $user->getId(),
            'sender' => User::rootUser()->getId(),
            'subject' => 'ACTION REQUIRED: Notification'
        ]);


        event(new SendMessage($message, $channel));
    }
}
