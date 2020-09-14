<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FoundDndSubscriberMessage
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;

    public $to;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $to, string $messageId)
    {
        $this->messageId = $messageId;
        $this->to = $to;
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
