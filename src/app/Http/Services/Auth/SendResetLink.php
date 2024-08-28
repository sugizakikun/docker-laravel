<?php

namespace App\Http\Services\Auth;

use App\Cognito\CognitoClient;

class SendResetLink
{
    private $cognitoClient;

    public function __construct(CognitoClient $cognitoClient)
    {
        $this->cognitoClient = $cognitoClient;
    }

    /**
     * @param string $email
     * @return string
     */
    public function execute(string $email)
    {
        return $this->cognitoClient->sendResetLink($email);
    }
}
