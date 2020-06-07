<?php

namespace App\Events;

use App\Contribution;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommissionEarned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $amount;

    public $contribution;

    /**
     * Create a new event instance.
     *
     * @param int               $amount
     * @param \App\Contribution $contribution
     */
    public function __construct(int $amount, Contribution $contribution)
    {
        $this->amount = $amount;
        $this->contribution = $contribution;
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
