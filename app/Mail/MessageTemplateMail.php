<?php

namespace App\Mail;

use App\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class MessageTemplateMail
 *
 * @package \App\Mail
 */
class MessageTemplateMail extends Mailable
{

    use Queueable, SerializesModels;

    private $messageModel;

    public function __construct(Message $message)
    {
        $this->messageModel = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $content = ($this->messageModel->message);

        return $this
            ->subject($this->messageModel->subject)
            ->view('emails.message-template', [
                'messageContent' => nl2br($content)
            ]);
    }
}
