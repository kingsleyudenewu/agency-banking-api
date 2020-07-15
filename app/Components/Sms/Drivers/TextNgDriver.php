<?php

namespace App\Components\Sms\Drivers;


use App\Services\Textng\TextNGSMSApi;

/**
 * Class TextNgDriver
 *
 * @package \App\Components\Sms\Drivers
 */
class TextNgDriver extends Driver
{
    protected  $api;

    public function __construct(TextNGSMSApi $api)
    {
        $this->api = $api;
    }

    /**
     * {@inheritdoc}
     */
    public function send()
    {
        $this->api->sendSms($this->recipient, $this->message);
    }
}
