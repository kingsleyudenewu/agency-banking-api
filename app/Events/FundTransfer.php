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

class FundTransfer
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $performedBy;
    public $customer;
    public $amount;
    public $remark;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $performedBy, User $customer, int $amount, string $remark = '')
    {
        $this->performedBy = $performedBy;
        $this->customer = $customer;
        $this->amount = $amount;
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
