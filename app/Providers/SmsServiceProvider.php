<?php

namespace App\Providers;

use App\Components\Sms\SmsManager;
use Illuminate\Support\ServiceProvider;

/**
 * Class SmsServiceProvider
 *
 * @package \App\Providers
 */
class SmsServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sms', function ($app) {
            return new SmsManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sms'];
    }
}
