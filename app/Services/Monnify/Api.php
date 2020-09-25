<?php

namespace App\Services\Monnify;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Class Api
 *
 * @package \App\Services\Monnify
 */
class Api
{

    const ENDPOINT_RESERVE_ACCOUNT_NUMBER = 'bank-transfer/reserved-accounts';
    const ENDPOINT_LOGIN = 'auth/login';
    const ENDPOINT_CHECK_TRANSACTION_STATUS = 'transactions/%s';

    /** @var string */
    private $logChannel = 'koloo';

    /** @var array */
    private $options;

    /** @var string */
    private $contract;

    /** @var string */
    private $apiKey;

    /** @var string */
    private $secretKey;

    /** @var string */
    private $baseUri;


    public function __construct(array $options)
    {
        $this->options = $options;
        $this->contract = array_get($options, 'contract');
        $this->apiKey = array_get($options, 'api_key');
        $this->secretKey = array_get($options, 'secret_key');
        $this->baseUri = array_get($options, 'base_uri');

        $this->httpClient = new Client(['base_uri' => $this->baseUri]);
    }


    public function verifyWebHook(array $webhook) : void
    {
        $string = implode("|", [
            $this->secretKey,
            array_get($webhook, 'paymentReference'),
            array_get($webhook, 'amountPaid'),
            array_get($webhook, 'paidOn'),
            array_get($webhook, 'transactionReference'),
        ]);

        $hash = hash('sha512', $string);

        $this->logInfo('verify webhook', [
            'calcHash' => $hash,
            'transactionHash' => array_get($webhook, 'transactionHash'),
            'webhook' => $webhook,
        ]);

        if(!hash_equals(array_get($webhook, 'transactionHash'), $hash)) {
            $this->logError('webhook not valid');
            throw new \Exception("Not valid transaction hash");
        }
    }

    /**
     * @param string $endpoint
     * @param array  $options
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    private function get(string $endpoint, array $options = [])
    {
        return $this->request('GET', $endpoint, $options);
    }

    /**
     * @param string $endpoint
     * @param array  $options
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    private function post(string $endpoint, array $options = [])
    {
        return $this->request('POST', $endpoint, $options);
    }

    /**
     * @param string $type
     * @param string $endpoint
     * @param array  $options
     *
     * @return mixed
     * @throws \Exception
     */
    private function request($type, string $endpoint, array $options = [])
    {
        $id = str_random();

        $this->logInfo("[{$id}] Request: [$type] [$endpoint]", $options);

        try {
            $response = $this->httpClient->request($type, $endpoint, $options);

            $result = \GuzzleHttp\json_decode($response->getBody()->getContents());

            $this->logInfo("[{$id}] Response: ", ['response' => $result]);
        }
        catch(\Exception $exception) {
            $this->logError("[{$id}] Exception message: {$exception->getMessage()}", ['exception' => $exception]);

            throw $exception;
        }

        return $result;
    }

    private function getAccessToken() : string {

        $options['headers']['Authorization'] = 'Basic ' . base64_encode($this->apiKey.':'.$this->secretKey);

       try {
           $res  = $this->post(static::ENDPOINT_LOGIN, $options);
           if(!$res->requestSuccessful)
           {
               throw new \Exception('Login failed');
           }
           return $res->responseBody->accessToken;
       } catch (\Exception $exception)
       {
           $this->logError("[MonnifyApi::getAccessToken] Exception message: {$exception->getMessage()}", ['exception' => $exception]);
           return "";
       }
    }

    /**
     * @param string      $accountName
     * @param string      $customerEmail
     * @param string|null $reference
     *
     * @return mixed
     * @throws \Exception
     */
    public function reserveAccountNumber(string $accountName, string $customerEmail, string $reference = null)
    {
        $options = [
            'json' => [
                'accountName' => $accountName,
                'accountReference' => $reference ?: str_random(32),
                'currencyCode' => 'NGN',
                'contractCode' => $this->contract,
                'customerEmail' => $customerEmail,
                'customerName' => $accountName,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken()
            ]
        ];

       try {
           return $this->post(static::ENDPOINT_RESERVE_ACCOUNT_NUMBER, $options)->responseBody;

       } catch (\Exception $exception)
       {
           $this->logError("[MonnifyApi::reserveAccountNumber] Exception message: {$exception->getMessage()}", ['exception' => $exception]);
           throw $exception;
       }
    }

    public function getSuccessfulTransaction(string $paymentReference)
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken()
            ]
        ];

        try {
            $body  = $this->get(sprintf(static::ENDPOINT_CHECK_TRANSACTION_STATUS, $paymentReference), $options);

            if($body->requestSuccessful && $body->responseCode === "0") {
                return $body->responseBody;
            };

            throw new \Exception('Transaction Status Check Failed.' . $body->responseMessage);

        } catch (\Exception $exception)
        {
            $this->logError("[MonnifyApi::checkTransactionStatus] Exception message: {$exception->getMessage()}", ['exception' => $exception]);
            throw $exception;
        }
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
        Log::channel($this->logChannel)->{$type}($message, $context);

        return $this;
    }
}
