<?php

namespace App\Http\Services\Profile;

use App\Cognito\CognitoClient;
use Illuminate\Support\Facades\Auth;

class UpdateProfile
{
    private $cognitoClient;

    public function __construct(CognitoClient $cognitoClient)
    {
        $this->cognitoClient = $cognitoClient;
    }

    /**
     * @param string|null $name
     * @param string $email
     * @return void
     */
    public function execute(?string $name, string $email)
    {
        $user = Auth::user();
        $oldEmail = $user->email;
        $result = 'updated';

        # Eメールアドレスに変更があればCognitoのユーザープールにも変更を通知する
        if ($oldEmail !== $email) {
            $attributes['email'] = $email;
            $result = $this->cognitoClient->setUserAttributes($oldEmail, $attributes);

            if ($result === CognitoClient::UPDATE_SUCCEED) {
                $user->email = $email;
            }
        }

        $user->name = $name;
        $user->save();

        return $result;
    }
}
