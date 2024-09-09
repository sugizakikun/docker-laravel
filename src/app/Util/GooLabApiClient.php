<?php

namespace App\Util;

use App\Util\Common\HttpClient;

class GooLabApiClient
{
    /**
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->prefix = 'https://labs.goo.ne.jp/api';
        $this->appId = env('GOO_LAB_APP_ID');
    }

    /**
     * @param string $sentence
     * @return mixed
     */
    public function morph(string $sentence)
    {
        $endPoint = $this->prefix.'/morph';

        $data = [
            "app_id" => $this->appId,
            "sentence" => $sentence,
        ];

        $jsonString =  $this->httpClient->post($endPoint, $data);
        $jsonData = json_decode($jsonString);
        $wordList = $jsonData->word_list[0];

        return array_map(function ($word) {
            return [
                'notation' => $word[0],
                'morpheme' => $word[1],
                'kana' => $word[2],
            ];
        }, $wordList);
    }
}
