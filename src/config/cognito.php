<?php

return [
    'region'            =>  env('AWS_DEFAULT_REGION', 'us-east-1'),
    'version'           =>  'latest',
    'app_client_id'     =>  env('AWS_COGNITO_CLIENT_ID'), // 作成したクライアントID
    'app_client_secret' =>   env('AWS_COGNITO_CLIENT_SECRET'), // 作成したクライアントシークレット
    'user_pool_id'      =>  env('AWS_COGNITO_USER_POOL_ID'), // ユーザープールのID
    'domain_name'       =>  env('AWS_COGNITO_DOMAIN_NAME'),
    'redirect_url'      =>  env('AWS_COGNITO_REDIRECT_URL', 'http://localhost:8080/auth/google/callback'), // 認証後リダイレクト先URL
];
