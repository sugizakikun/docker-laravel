<?php

namespace App\Http\Services;

use App\Models\User;
use App\Cognito\CognitoClient;
use Illuminate\Support\Facades\DB;

class DestroyUser
{
    public function __construct(CognitoClient $cognitoClient)
    {
        $this->cognitoClient = $cognitoClient;
    }

    public function execute(User $user)
    {
        try {
            DB::beginTransaction();
            $username = $user['cognito_username'];
            $this->cognitoClient->destroyUser($username);

            (new User())->newQuery()
                ->where('cognito_username', $username)
                ->delete();

            DB::commit();

            return true;
        } catch (\Exception $e){
            return false;
        }
    }
}
