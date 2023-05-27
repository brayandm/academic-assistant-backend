<?php

namespace App\Services;

use GuzzleHttp\{Client, RequestOptions};
use GuzzleHttp\Exception\GuzzleException;

class EngineService
{
    private $client;
    private $baseUrl;
    private $token;

    public function __construct()
    {
        $this->token=config('app.engine_api_token');
        $this->client = new Client(['headers'=>['X-API-Key'=>$this->token]]);
        $this->baseUrl = config('app.engine_api_url');
    }

    public function translate(string $originalLanguage, string $targetLanguage, string $textType, string $text)
    {
        $url = '/translate';

        try {
            $result = $this->client->post($this->baseUrl . $url, [
                RequestOptions::JSON => [
                    'original_language' => $originalLanguage,
                    'target_language' => $targetLanguage,
                    'text_type' => $textType,
                    'text' => $text,
                ]
            ]);
            $contents = json_decode($result->getBody()->getContents());
            return $contents;
        } catch (GuzzleException $e) {

        }
    }
}
