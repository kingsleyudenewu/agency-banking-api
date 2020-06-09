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

class UserAccountPasswordUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;


    public $password;

    /**
     * Create a new event instance.
     *
     * @param \App\Koloo\User $user
     * @param string          $password
     */
    public function __construct(User $user, string $password)
    {
        $this->user  = $user;
        $this->password = $password;
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
