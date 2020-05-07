<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AgentAccountCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $agent;

    /**
     * Create a new event instance.
     *
     * @param \App\Koloo\User $agent
     */
    public function __construct(\App\Koloo\User $agent)
    {
        $this->agent = $agent;
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
