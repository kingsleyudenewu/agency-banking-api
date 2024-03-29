<?php

namespace App\Listeners;

use App\Events\SendMessage;
use App\Koloo\User;
use App\Message;
use App\Services\Bitly\ShortUrl;
use App\Traits\LogTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPasswordResetNotification implements ShouldQueue
{
    use LogTrait;

    protected $shortUrl;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ShortUrl $shortUrl)
    {
        $this->shortUrl = $shortUrl;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->logChannel = 'SendPasswordResetNotification';
        $user = $event->user;
        $source = isset($event->source) ? $event->source : null;

        if(!$user)
        {
            $this->logInfo('User not found and can not continue with the notification');
            $this->logInfo($user);
            return;
        }

        if($user->isCustomer()) return;

        $channel = 'both';

        $passwordReset = $user->getNewPasswordReset();
        if(!$passwordReset) {
            $this->logInfo('Unable to generate password reset for user ' . $user->getId());
        }

        $url = settings('frontend_password_reset_base_url', env('APP_URL'));
        $url = sprintf('%s?code=%s&email=%s', $url, $passwordReset->plain_hash, $passwordReset->email);

        $expiresAt = $passwordReset->expires_at->diffForHumans();

        if($source === 'web')
        {
            $messageBody =$messageBody = sprintf(config('koloo.password_reset_message'), $this->shortUrl->get($url), $expiresAt);

        }
        else
        {
            $messageBody = sprintf(config('koloo.password_reset_message_new_customer'), $this->shortUrl->get($url), $expiresAt);
        }
        $message = Message::create([
            'message' => $messageBody,
            'message_type' => $channel,
            'user_id' => $user->getId(),
            'sender' => User::rootUser()->getId(),
            'subject' => 'Password reset'
        ]);

        event(new SendMessage($message, $channel));
    }
}
