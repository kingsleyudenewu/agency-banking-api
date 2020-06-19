<?php

namespace App\Components\Sms\Drivers;

use App\Services\Infobip\InfobipSMSApi;

/**
 * Class InfobipDriver
 *
 * @package \App\Components\Sms\Drivers
 */
class InfobipDriver extends Driver
{

    protected $infobipApi;

    public function __construct(InfobipSMSApi $infobipApi)
    {
        $this->infobipApi = $infobipApi;
    }

    /**
     * {@inheritdoc}
     */
    public function send()
    {

        $this->infobipApi->sendSms(
            $this->recipient,
            $this->message
        );
    }
}
