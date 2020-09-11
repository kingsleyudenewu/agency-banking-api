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

class WalletCredited
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $wallet;

    public $reason;

    public $amount;

    public $label;

    /**
     * Create a new event instance.
     *
     * @param \App\Koloo\Wallet $wallet
     * @param int               $amount
     * @param string            $reason
     * @param string            $label
     */
    public function __construct(Wallet $wallet, $amount, string $reason, string $label='')
    {
        $this->wallet = $wallet;
        $this->reason = $reason;
        $this->amount = $amount;
        $this->label = $label;
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
