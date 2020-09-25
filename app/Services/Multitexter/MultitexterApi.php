<?php

namespace App\Services\Multitexter;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


/**
 * @see https://www.multitexter.com/developers
 */
class MultitexterApi
{
    const SEND_SMS_ENDPOINT = 'https://app.multitexter.com/v2/app/sendsms';
    const SMS_STATUS_ENDPOINT = 'https://app.multitexter.com/v2/app/message/report';

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

    public function smsStatus(string $messageId)
    {
        $options = [
            'form_params' => [
                'msgids' => $messageId,
            ],
        ];

        return $this->post(static::SMS_STATUS_ENDPOINT, $options);
    }

    public function sendSms(string $to, string $message, string $from = null)
    {
        $options = [
            'form_params' => [
                'recipients' => $this->to ?: $to,
                'message' => $message,
                'sender_name' => $from ? : $this->from,
                'forcednd' => 1,
            ]
        ];

        $result = $this->post(static::SEND_SMS_ENDPOINT, $options);
        if(1 !== (int)$result->status) {
            throw new \Exception($result->msg, $result->status);
        }
    }

    /**
     * @param string $endpoint
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function get(string $endpoint, array $options = [])
    {
        return $this->request('GET', $endpoint, $options);
    }

    /**
     * @param string $endpoint
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function post(string $endpoint, array $options = [])
    {
        return $this->request('POST', $endpoint, $options);
    }

    /**
     * @param string $type
     * @param string $endpoint
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    private function request($type, string $endpoint, array $options = [])
    {
        $options['headers']['Authorization'] = 'Bearer ' . $this->apiKey;
        $options['headers']['Accept'] = 'application/json';

        $id = str_random();

        $this->logInfo("[{$id}] Request: [$type] [$endpoint]", $options);

        if ($this->debug) {
            $result = \GuzzleHttp\json_decode('{"status": 1}');
            return $result;
        }

        try {
            $response = $this->httpClient->request($type, $endpoint, $options);

            $result = \GuzzleHttp\json_decode($response->getBody()->getContents());
        }
        catch (\Exception $exception) {
            $this->logError("Exception message: {$exception->getMessage()}", ['exception' => $exception]);

            throw $exception;
        }

        $this->logInfo("[{$id}] Response: ", ['response' => $result]);

        return $result;
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
