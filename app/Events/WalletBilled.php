<?php

namespace App\Events;

use App\Koloo\Wallet;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalletBilled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $wallet;

    public $reason;

    /**
     * Create a new event instance.
     *
     * @param \App\Koloo\Wallet $wallet
     * @param string            $reason
     */
    public function __construct(Wallet $wallet, string $reason)
    {
        $this->wallet = $wallet;
        $this->reason = $reason;
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
