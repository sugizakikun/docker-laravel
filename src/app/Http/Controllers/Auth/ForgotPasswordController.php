<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Controller;
use App\Http\Services\Auth\SendResetLink;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

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
    public function sendResetLinkEmail(Request $request, SendResetLink $sendResetLink){

        $this->validateEmail($request);
        $email = $request->all()['email'];

        $response = $sendResetLink->execute($email);

        if($response != Password::RESET_LINK_SENT) {
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
