<?php

namespace App\Listeners;

use App\Events\SendMessage;
use App\Koloo\User;
use App\Message;
use App\Traits\LogTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySuperAgentOnAccountApproval implements ShouldQueue
{
    use LogTrait;

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
                $this->logChannel = 'NotifySuperAgentOnAccountApproval';
                $this->logInfo($event->user->getName() . ' has no parent');
                return;
        }

        if($user->isCustomer()) return;

        $channel = 'sms';

        $message = Message::create([
            'message' => sprintf(config('koloo.agent_account_approved_message'), $event->user->getName()),
            'message_type' => $channel,
            'user_id' => $user->getId(),
            'sender' => User::rootUser()->getId(),
            'subject' => 'Agent account approved'
        ]);

        event(new SendMessage($message, $channel));

    }
}
