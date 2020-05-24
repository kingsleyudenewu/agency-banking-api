<?php

namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PreSavingCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $requestData;

    public $authUser;

    /**
     * Create a new event instance.
     *
     * @param array $requestData
     * @param       $authUser
     */
    public function __construct( $requestData, $authUser )
    {
        $this->requestData = $requestData;
        $this->requestData = $authUser;
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
