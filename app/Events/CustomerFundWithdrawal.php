<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerFundWithdrawal
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $amount;

    public $customer;

    public $agent;

    /**
     * Create a new event instance.
     *
     * @param $amount
     * @param $customer
     * @param $agent
     */
    public function __construct($amount, $customer, $agent)
    {
        $this->amount = $amount;
        $this->customer = $customer;
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
