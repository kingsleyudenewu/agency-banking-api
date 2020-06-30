<?php

namespace App\Listeners;

use App\Events\SendMessage;
use App\Koloo\User;
use App\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySuperAgentOnAccountDisapproval implements ShouldQueue
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

        if(!$user || !$user->isAgent())
        {
            $this->logChannel = 'NotifySuperAgentOnAccountDisapproval';
            $this->logInfo($event->user->getName() . ' has no parent or this user is not an agent');
            return;
        }

        $channel = 'sms';

        $message = Message::create([
            'message' => sprintf(config('koloo.agent_account_disapproved_message'), $event->user->getName(), $event->remark),
            'message_type' => $channel,
            'user_id' => $user->getId(),
            'sender' => User::rootUser()->getId(),
            'subject' => 'Agent account not approved'
        ]);


        event(new SendMessage($message, $channel));
    }
}
