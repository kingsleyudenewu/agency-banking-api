<?php

namespace App\Listeners;

use App\Components\Sms\Facade\Sms;
use App\Mail\MessageTemplateMail;
use App\Message;
use App\Traits\LogTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

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
        } else if ($event->message->message_type === 'email') {
            $this->sendEmailMessage($event->message);
        } else if ($event->message->message_type === 'both') {
            $this->sendEmailMessage($event->message);
            $this->sendSMSMessage($event->message);
        }

        $event->message->status = Message::STATUS_SENT;
        $event->message->save();
    }

    protected function sendEmailMessage($message)
    {

        $to = $message->user->email;
        $mail = new MessageTemplateMail($message);

        $this->logInfo("[{$message->id}] Send email to {$to}.");

        if(env('APP_ENV') === 'local' || env('APP_ENV') === 'dev')
        {
            $this->logInfo('Application running on dev/staging. No sending our sms to external services');
            return true;
        }

        try {
            Mail::to($to)->send($mail);
        }
        catch (\Exception $exception) {
            $this->logError("[{$message->id}] SEND EMAIL ERROR: {$exception->getMessage()}");

            return false;
        }

        return true;

    }

    protected function sendSMSMessage($message)
    {
        $to = $message->user->phone;

        $text = trim(strip_tags($message->message));

        $this->logInfo("[{$message->id}] Send SMS to {$to}.");

        if(env('APP_ENV') === 'local' || env('APP_ENV') === 'dev')
        {
            $this->logInfo('Application running on dev/staging. No sending our sms to external services');
            $this->logInfo($text);
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
