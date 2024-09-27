<?php

namespace App\Util;

use App\Util\Common\HttpClient;

class GoogleAuthClient
{
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->appClientId = config('cognito.app_client_id');
        $this->appClientSecret = config('cognito.app_client_secret');
        $this->redirectUrl = config('cognito.redirect_url');
        $this->cognitoDomain = 'https://'. config('cognito.domain_name').'.auth.'.config('cognito.region').'.amazoncognito.com';
    }

    public function getToken(string $code)
    {
        $endPoint = $this->cognitoDomain . "/oauth2/token";

        $data = [
            'grant_type'    =>  'authorization_code',
            'client_id'     =>  $this->appClientId,
            'client_secret' =>  $this->appClientSecret,
            'code'          =>  $code,
            'redirect_uri'  =>  $this->redirectUrl
        ];
        $header = 'Content-type: application/x-www-form-urlencoded';

        $response  = $this->httpClient->post($endPoint, $data, $header, false);
        return  json_decode($response, true);
    }

    public function getUserInfo(array $getTokenResponse)
    {
        $endPoint = $this->cognitoDomain . "/oauth2/userInfo";

        $header = 'Authorization: Bearer '. $getTokenResponse['access_token'];

        $response = $this->httpClient->get($endPoint, $header);
        return  json_decode($response, true);
    }

}