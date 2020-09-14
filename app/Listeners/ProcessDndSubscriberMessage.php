<?php

namespace App\Listeners;

use App\Message;
use App\Components\Sms\Facade\Sms;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Traits\LogTrait;

class ProcessDndSubscriberMessage implements ShouldQueue
{
    use LogTrait;

    const BACKUP_CHANNEL_DRIVER = 'InfobipIntl';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->logInfo('listener now running');
        
        $msg = Message::find($event->messageId);
        
        if( $msg ) {
            
            try {
                Sms::channel(static::BACKUP_CHANNEL_DRIVER)
                    ->to($event->to)
                    ->content($msg->message)
                    ->send($msg->id);
            }
            catch (\Exception $exception) {
                $this->logError("[{$msg->id}] SEND SMS ERROR: {$exception->getMessage()}");
            }
        }
    }
}
