<?php
namespace App\Cognito;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;

class CognitoClient
{
    protected $client;
    protected $clientId;
    protected $clientSecret;
    protected $poolId;

    /**
     * CognitoClient constructor
     */
    public function __construct(
        CognitoIdentityProviderClient $client,
                                      $clientId,
                                      $clientSecret,
                                      $poolId
    )
    {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->poolId = $poolId;
    }

    // 追加

    /**
     * register
     */
    public function register($email, $password, $attributes = [])
    {
        try {
            $response = $this->client->signUp([
                'ClientId' => $this->clientId,
                'Password' => $password,
                'SecretHash' => $this->cognitoSecretHash($email),
                'UserAttributes' => $this->formatAttributes($attributes),
                'Username' => $email
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw $e;
        }
        return $response['UserSub'];
    }

    /**
     * cognitoSecretHash
     */
    protected function cognitoSecretHash($username)
    {
        return $this->hash($username . $this->clientId);
    }

    /**
     * hash
     */
    protected function hash($message)
    {
        $hash = hash_hmac(
            'sha256',
            $message,
            $this->clientSecret,
            true
        );
        return base64_encode($hash);
    }

    /**
     * formatAttributes
     * attributesを保存用に整形
     */
    protected function formatAttributes(array $attributes)
    {
        $userAttributes = [];
        foreach ($attributes as $key => $value) {
            $userAttributes[] = [
                'Name' => $key,
                'Value' => $value,
            ];
        }
        return $userAttributes;
    }

    /**
     * getUser
     * メールアドレスからユーザー情報を取得する
     */
    public function getUser($username)
    {
        try {
            $user = $this->client->adminGetUser([
                'Username' => $username,
                'UserPoolId' => $this->poolId,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            return false;
        }
        return $user;
    }
}
