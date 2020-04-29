<?php

namespace App\Components\Sms\Drivers;

use App\Services\Multitexter\MultitexterApi;

/**
 * Class MultitexterDriver
 *
 * @package \App\Components\Sms\Drivers
 */
class MultitexterDriver extends Driver
{
    /**
     * The Multitexter client.
     *
     * @var MultitexterApi
     */
    protected $multitexterApi;

    /**
     * The phone number this sms should be sent from.
     *
     * @var string
     */
    protected $from;

    /**
     * Create a new Multitexter driver instance.
     *
     * @param  \App\Services\Multitexter\MultitexterApi  $multitexterApi
     * @param  string  $from
     * @return void
     */
    public function __construct(MultitexterApi $multitexterApi)
    {
        $this->multitexterApi = $multitexterApi;
    }

    /**
     * {@inheritdoc}
     */
    public function send()
    {
        $this->multitexterApi->sendSms(
            $this->recipient,
            $this->message
        );
    }

}
