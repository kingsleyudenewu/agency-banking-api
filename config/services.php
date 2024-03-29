<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'monnify' => [
        'contract' => env('MONNIFY_CONTRACT'),
        'api_key' => env('MONNIFY_API_KEY'),
        'secret_key' => env('MONNIFY_SECRET_KEY'),
        'base_uri' => env('MONNIFY_BASE_URI'),
    ],

    'infobip' => [
        'api_key' => env('INFOBIP_API_KEY'),
        'base_uri' => env('INFOBIP_BASE_URL'),
    ],

    'bitly' => [
        'access_token' => env('BITLY_ACCESS_TOKEN'),
        'base_url' => env('BITLY_BASE_URL')
    ]

];
