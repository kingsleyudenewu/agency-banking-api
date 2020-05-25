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

class BalanceUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $amount;

    public $transType;

    public $toAccount;

    public $performedBy;


    /**
     * Create a new event instance.
     *
     * @param int             $amount
     * @param string          $type
     * @param \App\Koloo\User $toAccount
     * @param \App\Koloo\User $performedBy
     */
    public function __construct(int $amount, string $type, User $toAccount, User $performedBy)
    {
        $this->amount = $amount;
        $this->transType = $type;
        $this->toAccount = $toAccount;
        $this->performedBy = $performedBy;
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
