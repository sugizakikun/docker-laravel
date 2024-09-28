<?php

namespace App\Http\Services\Auth;

use App\Cognito\CognitoClient;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;

class CreateUser
{
    private $cognitoClient;

    public function __construct(CognitoClient $cognitoClient)
    {
        $this->cognitoClient = $cognitoClient;
    }

    /**
     * @param array $data
     * @return void
     */
    public function execute(array $data)
    {
        DB::beginTransaction();

        // Cognito側の新規登録
        $username = $this->cognitoClient->register(
            $data['email'],
            $data['password'],
            [
                'email' => $data['email'],
            ]
        );

        // Laravel側の新規登録
        $user = $this->store($data, $username);
        event(new Registered($user));

        DB::commit();
    }

    /**
     * @param array $data
     * @param $username
     * @return mixed
     */
    public function store(array $data, $username)
    {
        return User::create([
            'cognito_username' => $username,
            'email'            => $data['email'],
        ]);
    }
}
