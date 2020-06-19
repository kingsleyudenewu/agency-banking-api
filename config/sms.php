<?php

return [

    'default' => env('SMS_DRIVER', 'null'),

    'multitexter' => [
        'api_key' => env('MULTITEXTER_API_KEY'),
        'to' => env('MULTITEXTER_TO'),
        'debug' => env('MULTITEXTER_DEBUG'),
        'from' => env('MULTITEXTER_FROM'),
    ],

    'infobip' => [
        'api_key' => env('INFOBIP_API_KEY'),
        'from' => env('INFOBIP_SMS_FROM', 'Koloo')

    ]


];
