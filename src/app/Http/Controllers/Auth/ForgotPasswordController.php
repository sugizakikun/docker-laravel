<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Cognito\CognitoClient;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    private $cognitoClient;

    public function __construct(CognitoClient $cognitoClient)
    {
        $this->cognitoClient = $cognitoClient;
    }

    // つまりこのメソッドをCognito連携用に上書きすりゃいいってことか！！！！
    // この時に ForgotPassword APIを呼び出せばいいてことだな？
    // 参考リンク：https://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_ForgotPassword.html
    public function sendResetLinkEmail(Request $request){

        $this->validateEmail($request);
        $email = $request->all()['email'];

        $response = $this->cognitoClient->sendResetLink($email);

        if($response != Password::RESET_LINK_SENT)
        {
            return $this->sendResetLinkFailedResponse($request, $response);
        }
        else {
            $this->broker()->sendResetLink(
                $this->credentials($request)
            );

            return  $this->sendResetLinkResponse($request, $response);
        }
    }
}
