<?php

namespace App\Services\Textng;

use Illuminate\Support\Facades\Log;

/**
 * Class TextNGSMSApi
 *
 * @package \App\Services\Textng
 */
class TextNGSMSApi
{
    const SEND_SMS_ENDPOINT = 'https://textng.xyz/api/sendsms/';

    /** @var string */
    private $apiKey;

    /** @var string */
    private $logChannel = 'koloo';


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

    private $bypasscode;

    private $route;

    /**
     * SlingApi constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->apiKey = array_get($options, 'api_key');
        $this->to = array_get($options, 'to');
        $this->from = array_get($options, 'from');
        $this->bypasscode = array_get($options, 'bypasscode');
        $this->route = array_get($options, 'route');
        $this->debug = array_get($options, 'debug', false);
    }


    public function sendSms(string $to, string $message, string $from = null, $flash=false)
    {

        $curl = sprintf('%s?key=%s&sender=%s&phone=%s&message=%s&route=%s&bypasscode=%s',
        static::SEND_SMS_ENDPOINT, $this->apiKey, $this->from, $to, urlencode($message),$this->route,$this->bypasscode);
        $this->logInfo('Sending --> ' . $curl);

        $ch = curl_init($curl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        $this->logInfo($response);
        return $response;
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
