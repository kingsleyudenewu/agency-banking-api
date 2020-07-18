<?php

namespace App\Events;

use App\Saving;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SweepSaving
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $saving;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Saving $saving)
    {
        $this->saving = $saving;
    }


}
