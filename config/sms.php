<?php

return [

    'default' => env('SMS_DRIVER', 'null'),

    'multitexter' => [
        'api_key' => env('MULTITEXTER_API_KEY'),
        'to' => env('MULTITEXTER_TO'),
        'debug' => env('MULTITEXTER_DEBUG'),
        'from' => env('MULTITEXTER_FROM'),
    ],


];
