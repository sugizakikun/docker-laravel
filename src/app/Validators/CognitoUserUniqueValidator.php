<?php
namespace App\Validators;

use Illuminate\Auth\AuthManager;

class CognitoUserUniqueValidator {

    public function __construct(AuthManager $AuthManager)
    {
        $this->AuthManager = $AuthManager;
    }

    public function validate($attribute, $value, $parameters, $validator)
    {
        $cognitoUser = $this->AuthManager->getCognitoUser($value);

        if ($cognitoUser) {
            return false;
        }
        return true;
    }
}
