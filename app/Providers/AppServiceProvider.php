<?php

namespace App\Providers;

use App\Services\Bitly\ShortUrl;
use App\Services\Monnify\Api as MonnifyApi;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        Validator::extend('strong_password', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/', (string)$value);
        }, 'Password is too weak. Must contain a special character, capital letters, numbers and small letters.');


        Validator::extend('older_than', function($attribute, $value, $parameters)
        {
            $minAge = ( ! empty($parameters)) ? (int) $parameters[0] : config('koloo.min_age');
            return Carbon::now()->diff(new Carbon($value))->y >= $minAge;
        }, 'You must at least ' . config('koloo.min_age') . ' years or older');


        $this->app->bind(MonnifyApi::class, function () {
            return new MonnifyApi(config('services.monnify'));
        });

        $this->app->bind(ShortUrl::class, function(){
            return new ShortUrl(
                config('services.bitly.base_url'),
                config('services.bitly.access_token')
            );
        });
    }
}
