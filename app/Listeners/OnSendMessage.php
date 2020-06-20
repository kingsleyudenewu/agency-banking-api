<?php

namespace App\Listeners;

use App\Components\Sms\Facade\Sms;
use App\Message;
use App\Traits\LogTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OnSendMessage implements ShouldQueue
{
    use LogTrait;




    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->logChannel = 'koloo';
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->message->status !== Message::STATUS_NEW) {
            $this->logInfo("[{$event->message->id}] message status must be [".Message::STATUS_NEW."]");

            return;
        }

        if ($event->message->message_type === 'sms') {
            $this->sendSMSMessage($event->message);
        }

        $event->message->status = Message::STATUS_SENT;
        $event->message->save();
    }

    protected function sendSMSMessage($message)
    {
        $to = $message->user->phone;

        $text = trim(strip_tags($message->message));

        $this->logInfo("[{$message->id}] Send SMS to {$to}.");

        if(env('APP_ENV') === 'local' || env('APP_ENV') === 'dev')
        {
            $this->logInfo('Application running on dev/staging. No sending our sms to external services');
            return true;
        }

        try {
            Sms::to($to)
                ->content($text)
                ->send();
        }
        catch (\Exception $exception) {
            $this->logError("[{$message->id}] SEND SMS ERROR: {$exception->getMessage()}");
            return false;
        }

        return true;
    }



}
