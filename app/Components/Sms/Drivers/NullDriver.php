<?php

namespace App\Components\Sms\Drivers;

/**
 * Class NullDriver
 *
 * @package \App\Components\Sms\Drivers
 */
class NullDriver extends Driver
{

    /**
     * {@inheritdoc}
     */
    public function send()
    {
        return [];
    }
}
