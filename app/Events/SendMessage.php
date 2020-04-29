<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Message;

class SendMessage
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public $channel;

    /**
     * Create a new event instance.
     *
     * @param \App\Message $message
     * @param string       $channel
     */
    public function __construct(Message $message, string $channel)
    {
        $this->message = $message;
        $this->channel = $channel;
    }


}
