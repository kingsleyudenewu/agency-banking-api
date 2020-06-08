<?php

namespace App\Events;

use App\Koloo\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $remark;

    /**
     * Create a new event instance.
     *
     * @param \App\Koloo\User $user
     * @param string          $remark
     */
    public function __construct(User $user,  string $remark='')
    {
        $this->user = $user;
        $this->remark = $remark;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
