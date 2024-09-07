<?php

namespace App\Util;

use App\Util\Common\HttpClient;

class NsfwApiClient
{
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->prefix = 'http://'.config('fargate.task_ip_address').':5000';
    }

    /**
     * @param string $s3Path
     * @return mixed
     */
    public function singlePrediction(string $s3Path)
    {
        $endPoint = $this->prefix
            .'/?url='
            .urlencode($s3Path);

        $jsonString = $this->httpClient->get($endPoint);
        return  json_decode($jsonString, true);
    }

    /**
     * @param array $s3Paths
     * @return mixed
     */
    public function batchPrediction(array $s3Paths)
    {
        $endPoint = $this->prefix .'/batch-classify';
        $data = [
            "images" =>  $s3Paths
        ];

        $jsonString = $this->httpClient->post($endPoint, $data);
        $jsonData =  json_decode($jsonString, true);

        return $jsonData['predictions'];
    }
}
