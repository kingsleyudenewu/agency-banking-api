<?php

namespace App\Events;

use App\Koloo\User;
use App\OTP;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNewOTP
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $otp;

    public $user;

    /**
     * Create a new event instance.
     *
     * @param \App\OTP        $otp
     * @param \App\Koloo\User $user
     */
    public function __construct(OTP $otp, User $user)
    {
        $this->otp = $otp;
        $this->user = $user;
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
