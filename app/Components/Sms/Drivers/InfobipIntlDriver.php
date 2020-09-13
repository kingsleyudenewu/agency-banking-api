<?php

namespace App\Components\Sms\Drivers;

use App\Services\Infobip\InfobipIntlSMSApi;

/**
 * Class InfobipDriver
 *
 * @package \App\Components\Sms\Drivers
 */
class InfobipIntlDriver extends Driver
{

    protected $infobipIntlApi;

    public function __construct(InfobipIntlSMSApi $infobipIntlApi)
    {
        $this->infobipIntlApi = $infobipIntlApi;
    }

    /**
     * {@inheritdoc}
     */
    public function send($messageId)
    {

        $this->infobipIntlApi->sendSms(
            $this->recipient,
            $this->message,
            $messageId
        );
    }
}
