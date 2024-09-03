<?php
namespace App\Cognito;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;

class CognitoClient
{
    const NEW_PASSWORD_CHALLENGE = 'NEW_PASSWORD_REQUIRED';
    const FORCE_PASSWORD_STATUS  = 'FORCE_CHANGE_PASSWORD';
    const RESET_REQUIRED         = 'PasswordResetRequiredException';
    const USER_NOT_FOUND         = 'UserNotFoundException';
    const USERNAME_EXISTS        = 'UsernameExistsException';
    const INVALID_PASSWORD       = 'InvalidPasswordException';
    const CODE_MISMATCH          = 'CodeMismatchException';
    const EXPIRED_CODE           = 'ExpiredCodeException';
    const LIMIT_EXCEEDED         = 'LimitExceededException';

    const TOO_MANY_REQUESTS_EXCEPTION = 'TooManyRequestsException';

    const INTERNAL_ERROR         = 'InternalErrorException';

    const ALIAS_EXISTS           = 'AliasExistsException';

    const UPDATE_SUCCEED         = 'Update has been Successful!';

    /**
     * @var CognitoIdentityProviderClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $poolId;

    /**
     * CognitoClient constructor.
     * @param CognitoIdentityProviderClient $client
     * @param string $clientId
     * @param string $clientSecret
     * @param string $poolId
     */
    public function __construct(
        CognitoIdentityProviderClient $client,
                                      $clientId,
                                      $clientSecret,
                                      $poolId
    ) {
        $this->client       = $client;
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->poolId       = $poolId;
    }

    /**
     * Checks if credentials of a user are valid
     *
     * @see http://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminInitiateAuth.html
     * @param string $email
     * @param string $password
     * @return \Aws\Result|bool
     */
    public function authenticate($email, $password)
    {
        try
        {
            $response = $this->client->adminInitiateAuth([
                'AuthFlow'       => 'ADMIN_NO_SRP_AUTH',
                'AuthParameters' => [
                    'USERNAME'     => $email,
                    'PASSWORD'     => $password,
                    'SECRET_HASH'  => $this->cognitoSecretHash($email)
                ],
                'ClientId'   => $this->clientId,
                'UserPoolId' => $this->poolId
            ]);
        }
        catch (CognitoIdentityProviderException $exception)
        {
            if ($exception->getAwsErrorCode() === self::RESET_REQUIRED ||
                $exception->getAwsErrorCode() === self::USER_NOT_FOUND) {
                return false;
            }

            throw $exception;
        }

        return $response;
    }

    /**
     * Registers a user in the given user pool
     *
     * @param $email
     * @param $password
     * @param array $attributes
     * @return bool
     */
    public function register($email, $password, array $attributes = [])
    {
        $attributes['email'] = $email;

        try
        {
            $response = $this->client->signUp([
                'ClientId' => $this->clientId,
                'Password' => $password,
                'SecretHash' => $this->cognitoSecretHash($email),
                'UserAttributes' => $this->formatAttributes($attributes),
                'Username' => $email
            ]);
        }
        catch (CognitoIdentityProviderException $e) {
            if ($e->getAwsErrorCode() === self::USERNAME_EXISTS) {
                return false;
            }

            throw $e;
        }

        return $response['UserSub'];
    }

    /**
     * Reset a users password based on reset code.
     * http://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_ConfirmForgotPassword.html.
     *
     * @param string $code
     * @param string $username
     * @param string $password
     * @return string
     */
    public function resetPassword($code, $username, $password)
    {
        try {
            $this->client->confirmForgotPassword([
                'ClientId' => $this->clientId,
                'ConfirmationCode' => $code,
                'Password' => $password,
                'SecretHash' => $this->cognitoSecretHash($username),
                'Username' => $username,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            $errorCode = $e->getAwsErrorCode();

            if($errorCode=== self::USER_NOT_FOUND) {
                return Password::INVALID_USER;
            }

            if($errorCode === self::INVALID_PASSWORD) {
                return Lang::has('passwords.password')
                    ? 'passwords.password'
                    : $e->getAwsErrorMessage();
            }

            if($errorCode=== self::CODE_MISMATCH) {
                return Password::INVALID_TOKEN;
            }

            if($errorCode === self::LIMIT_EXCEEDED){
                return Password::RESET_THROTTLED;
            }

            if($errorCode === self::EXPIRED_CODE ) {
                return $e->getAwsErrorMessage();
            }

            throw $e;
        }

        return Password::PASSWORD_RESET;
    }

    /**
     * Send a password reset code to a user.
     * @see http://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_ForgotPassword.html
     *
     * @param  string $username
     * @return string
     */
    public function sendResetLink($username)
    {
        try {
            $user = $this->getUser($username);

            if(!$user){
                return Password::INVALID_USER;
            }

            $userAttributes = $this->formatKeyValue($user['UserAttributes']);

            if($userAttributes['email_verified'] == 'false'){
                return 'email is not verified';
            }

            $result = $this->client->forgotPassword([
                'ClientId' => $this->clientId,
                'SecretHash' => $this->cognitoSecretHash($username),
                'Username' => $username,
            ]);

        } catch (CognitoIdentityProviderException $e) {
            $errorCode = $e->getAwsErrorCode();

            if ($errorCode === self::LIMIT_EXCEEDED ) {
                return Password::RESET_THROTTLED;
            }

            throw $e;
        }

        return Password::RESET_LINK_SENT;
    }

    /**
     * Destroy a user from cognito user pool.
     *  @see https://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminDeleteUser.html
     *
     * @param $username
     * @return string|true|null
     */
    public function destroyUser($username)
    {
        try {
            $result = $this->client->adminDeleteUser([
                'Username' => $username,
                'UserPoolId' => $this->poolId
            ]);
        } catch (CognitoIdentityProviderException $e) {
            return false;
        }

        return true;
    }

    # HELPER FUNCTIONS

    /**
     * Set a users attributes.
     * http://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminUpdateUserAttributes.html
     *
     * @param string $username
     * @param array  $attributes
     * @return bool
     */
    public function setUserAttributes($username, array $attributes)
    {
        try {
            $response = $this->client->AdminUpdateUserAttributes([
                'Username' => $username,
                'UserPoolId' => $this->poolId,
                'UserAttributes' => $this->formatAttributes($attributes),
            ]);
        } catch (CognitoIdentityProviderException $e) {
            return $e->getAwsErrorMessage();
        }

        return self::UPDATE_SUCCEED;
    }


    /**
     * Creates the Cognito secret hash
     * @param string $username
     * @return string
     */
    protected function cognitoSecretHash($username)
    {
        return $this->hash($username . $this->clientId);
    }

    /**
     * Creates a HMAC from a string
     *
     * @param string $message
     * @return string
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
     * Get user details.
     * http://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_GetUser.html
     *
     * @param  string $username
     * @return mixed
     */
    public function getUser($username)
    {
        try {
            $user = $this->client->AdminGetUser([
                'Username' => $username,
                'UserPoolId' => $this->poolId,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            return false;
        }

        return $user;
    }

    /**
     * Format attributes in Name/Value array
     *
     * @param  array $attributes
     * @return array
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

    private function formatKeyValue(array $userAttributes)
    {
        $attributes = [];

        foreach($userAttributes as $userAttribute){
            $attributes[$userAttribute['Name']] = $userAttribute['Value'];
        }

        return $attributes;
    }
}
