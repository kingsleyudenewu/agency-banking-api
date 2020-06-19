<?php

namespace App\Services\Infobip;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Class InfobipSMSApi
 *
 * @package \App\Services\Infobip
 */
class InfobipSMSApi
{
    const SEND_SMS_ENDPOINT = 'https://6jj6wr.api.infobip.com/sms/2/text/advanced';

    /** @var string */
    private $apiKey;

    /** @var string */
    private $logChannel = 'infobip-requests';


    /**
     * Global to
     * @var null|string
     */
    private $to;

    /**
     * Debug mode. In debug mode it does not send request to API
     * @var bool
     */
    private $debug = false;

    /**
     * SlingApi constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->apiKey = array_get($options, 'api_key');
        $this->to = array_get($options, 'to');
        $this->from = array_get($options, 'from');
        $this->debug = array_get($options, 'debug', false);

        $this->httpClient = new Client();
    }


    public function sendSms(string $to, string $message, string $from = null, $flash=false)
    {
        $id = sprintf("%s%s", str_random(32), time());
        $options = [
            'messages' => [
                    [
                        'from' => $from ? : $this->from,
                        'destinations' => [
                            ['to' => $this->to ?: $to, 'messageId' => $id]
                        ],
                        'text' => $message,
                        'flash' => $flash,
                        'validityPeriod' => 720
                    ]
                ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => static::SEND_SMS_ENDPOINT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($options),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: App ". $this->apiKey,
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        $this->logInfo($response);
    }





    private function logInfo($message, array $context = []): self
    {
        return $this->log('info', $message, $context);
    }

    private function logError(string $message = '', array $context = []): self
    {
        $message = "Response: {$message}";

        return $this->log('error', $message, $context);
    }

    private function log(string $type, string $message, array $context = []): self
    {
        $message = $this->debug ? "[DEBUG MODE] " . $message : $message;

        Log::channel($this->logChannel)->{$type}($message, $context);

        return $this;
    }

}
