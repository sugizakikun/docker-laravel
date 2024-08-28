<?php

namespace App\Http\Services\Auth;

use App\Cognito\CognitoClient;
use App\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class ResetPassword
{
    private $cognitoClient;

    public function __construct(CognitoClient $cognitoClient)
    {
        $this->cognitoClient = $cognitoClient;
    }

    /**
     * @param string $code
     * @param string $email
     * @param string $password
     * @return mixed
     */
    public function execute(string $code, string $email, string $password)
    {
        DB::beginTransaction();

        try {
            $response = $this->cognitoClient->resetPassword($code, $email, $password);

            if($response != Password::PASSWORD_RESET){
                return $response;
            }

            $this->deletePasswordReset($email);

            DB::commit();
        } catch (\Exception $e){
            DB::rollBack();
        }

        return $response;
    }


    /**
     * @param string $email
     * @return bool
     */
    public function hasExpired(string $email) : bool
    {
        $passwordReset = PasswordReset::where('email', $email)->first();

        if(!$passwordReset){
            return true;
        } else {
            $createdAt = new Carbon($passwordReset->created_at);

            return $createdAt->addMinutes(10)->isPast();
        }
    }

    /**
     * @param string $email
     * @return void
     */
    private function deletePasswordReset(string $email) :void
    {
        PasswordReset::where('email', $email)->delete();
    }
}
