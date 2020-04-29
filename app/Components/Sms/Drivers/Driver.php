<?php

namespace App\Components\Sms\Drivers;

use App\Components\Sms\Exceptions\SmsException;

/**
 * Class Driver
 *
 * @package \App\Components\Sms\Drivers
 */
abstract class Driver
{

    /**
     * The recipient of the message.
     *
     * @var string
     */
    protected $recipient;

    /**
     * The message to send.
     *
     * @var string
     */
    protected $message;

    /**
     * {@inheritdoc}
     */
    abstract public function send();

    /**
     * Set the recipient of the message.
     *
     * @param string $recipient
     *
     * @return $this
     * @throws \Throwable
     */
    public function to(string $recipient)
    {
        throw_if(is_null($recipient), SmsException::class, 'Recipients cannot be empty');

        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Set the content of the message.
     *
     * @param string $message
     *
     * @return $this
     * @throws \Throwable
     */
    public function content(string $message)
    {
        throw_if(empty($message), SmsException::class, 'Message text is required');

        $this->message = $message;

        return $this;
    }
}
