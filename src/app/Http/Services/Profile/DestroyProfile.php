<?php

namespace App\Http\Services\Profile;

use App\Models\User;
use App\Cognito\CognitoClient;

class DestroyProfile
{
    public function __construct(CognitoClient $cognitoClient)
    {
        $this->cognitoClient = $cognitoClient;
    }

    public function execute(User $user)
    {
        try {
            $username = $user['cognito_username'];
            $isSucceed = $this->cognitoClient->destroyUser($username);

            if($isSucceed === true) {
                (new User())->newQuery()
                    ->where('cognito_username', $username)
                    ->first()
                    ->delete();
            }
        } catch (\Exception $e){
            return false;
        }

        return true;
    }
}
