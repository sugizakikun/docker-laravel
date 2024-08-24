<?php

namespace App\Http\Services;

class CreateGoogleAuthUrl
{
    public function execute()
    {
        $prefix = $_SERVER['HTTP_HOST'] === 'localhost:8080'
            ? 'http://' : 'https://';

        $params = [
            '{domain_name}' => config('cognito.domain_name'),
            '{region}' => config('cognito.region'),
            '{client_id}' => config('cognito.app_client_id'),
            '{redirect_url}' =>  $prefix . $_SERVER['HTTP_HOST'] . '/home'
        ];

        return str_replace(array_keys($params), array_values($params), config('cognito.google_auth_url') );
    }
}
