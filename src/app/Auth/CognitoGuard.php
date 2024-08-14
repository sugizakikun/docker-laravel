<?php
namespace App\Auth;

use App\Cognito\CognitoClient;
use App\Exceptions\InvalidUserModelException;
use App\Exceptions\NoLocalUserException;
use Aws\Result;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Request;

class CognitoGuard extends SessionGuard implements StatefulGuard
{
    private $client;
    protected $provider;
    protected $session;
    protected $request;

    /**
     * CognitoGuard constructor.
     */
    public function __construct(
        string $name,
        CognitoClient $client,
        UserProvider $provider,
        Session $session,
        ?Request $request = null
    ) {
        $this->client = $client;
        parent::__construct($name, $provider, $session, $request);
    }

    /**
     * register
     * ユーザーを新規登録
     */
    public function register($email, $pass, $attributes = [])
    {
        $username = $this->client->register($email, $pass, $attributes);
        return $username;
    }

    /**
     * @param mixed $user
     * @param array $credentials
     * @return bool
     * @throws InvalidUserModelException
     */
    protected function hasValidCredentials($user, $credentials)
    {
        /** @var Result $response */
        $result = $this->client->authenticate($credentials['email'], $credentials['password']);

        if ($result && $user instanceof Authenticatable) {
            return true;
        }

        return false;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool   $remember
     * @throws
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        $this->fireAttemptEvent($credentials, $remember);

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            $this->login($user, $remember);
            return true;
        }

        $this->fireFailedEvent($user, $credentials);

        return false;
    }

    /**
     * getCognitoUser
     * メールアドレスからCognitoのユーザー名を取得
     */
    public function getCognitoUser($email)
    {
        return $this->client->getUser($email);
    }
}
