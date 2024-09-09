<?php

namespace App\Http\Services\Profile;

use App\Cognito\CognitoClient;
use App\Http\Services\Common\NgWordMaskingTrait;
use App\Util\GooLabApiClient;
use Illuminate\Support\Facades\Auth;

class UpdateProfile
{
    use NgWordMaskingTrait;

    /**
     * @var GooLabApiClient
     */
    protected $gooApiClient;

    /**
     * @var CognitoClient
     */
    private $cognitoClient;

    public function __construct(
        CognitoClient $cognitoClient,
        GooLabApiClient $gooApiClient
    )
    {
        $this->cognitoClient = $cognitoClient;
        $this->gooApiClient = $gooApiClient;
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

        $formattedWordList = $this->gooApiClient->morph($name);
        $maskedSentence = $this->maskingProcess($formattedWordList);


        $user->name = $maskedSentence;
        $user->save();

        return $result;
    }
}
