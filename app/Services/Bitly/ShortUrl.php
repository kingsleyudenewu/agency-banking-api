<?php

namespace App\Services\Bitly;

use App\Traits\LogTrait;
use GuzzleHttp\Client;

/**
 * Class ShortUrl
 *
 * @package \App\Services\Bitly
 */
class ShortUrl
{
    use LogTrait;

    private $accessToken;

    private $baseUrl;

    public function __construct(string $baseUrl, string $accessToken)
    {
        $this->accessToken = $accessToken;
        $this->baseUrl = $baseUrl;

        $this->logChannel = 'Bitly';
    }

    public function get(string $longUrl): string
    {
        $ch = curl_init($this->baseUrl);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['long_url' => $longUrl]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $this->accessToken",
            "Content-Type: application/json"
        ]);

        $arrResult = json_decode(curl_exec($ch));
        if($arrResult && isset($arrResult->link))
        {
            return $arrResult->link;
        }

        return $longUrl;
    }
}
