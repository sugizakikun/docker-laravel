<?php

return [
    'region'            =>  env('AWS_DEFAULT_REGION', 'us-east-1'),
    'version'           =>  'latest',
    'app_client_id'     =>  env('AWS_COGNITO_CLIENT_ID'), // 作成したクライアントID
    'app_client_secret' =>   env('AWS_COGNITO_CLIENT_SECRET'), // 作成したクライアントシークレット
    'user_pool_id'      =>  env('AWS_COGNITO_USER_POOL_ID'), // ユーザープールのID
    'domain_name'       =>  env('AWS_COGNITO_DOMAIN_NAME'),
    'redirect_url'      =>  env('AWS_COGNITO_REDIRECT_URL', 'http://localhost:8080/auth/google'), // 認証後リダイレクト先URL
    'google_auth_url'  => "https://{domain_name}.auth.{region}.amazoncognito.com/oauth2/authorize?identity_provider=Google&redirect_uri={redirect_url}&response_type=CODE&client_id={client_id}&scope=email openid"
];
