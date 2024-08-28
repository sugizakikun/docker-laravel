<?php

namespace App\Http\Services\Auth;

class CreateGoogleAuthUrl
{
    public function execute()
    {
        $params = [
            '{domain_name}' => config('cognito.domain_name'),
            '{region}' => config('cognito.region'),
            '{client_id}' => config('cognito.app_client_id'),
            '{redirect_url}' => 'http://localhost:8080/home'
        ];

        return str_replace(array_keys($params), array_values($params), config('cognito.google_auth_url') );
    }
}
