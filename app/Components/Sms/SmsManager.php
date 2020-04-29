<?php

namespace App\Components\Sms;

use App\Components\Sms\Drivers\MultitexterDriver;
use App\Services\Multitexter\MultitexterApi;
use App\Components\Sms\Drivers\NullDriver;


use Illuminate\Support\Manager;


/**
 * Class SmsManager
 *
 * @package \App\Components\Sms
 */
class SmsManager extends Manager
{

    /**
     * Get a driver instance.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function channel($name = null)
    {
        return $this->driver($name);
    }



    /**
     * Create a Multitexter SMS driver instance.
     *
     * @return \App\Components\Sms\Drivers\MultitexterDriver
     */
    public function createMultitexterDriver()
    {
        return new MultitexterDriver(
            $this->createMultitexterClient()
        );
    }



    /**
     * Create the Multitexter client.
     *
     * @return MultitexterApi
     */
    protected function createMultitexterClient()
    {
        return new MultitexterApi(config('sms.multitexter'));
    }


    /**
     * Create a Null SMS driver instance.
     *
     * @return \App\Components\Sms\Drivers\NullDriver
     */
    public function createNullDriver()
    {
        return new NullDriver;
    }

    /**
     * Get the default SMS driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['sms.default'] ?? 'null';
    }
}
