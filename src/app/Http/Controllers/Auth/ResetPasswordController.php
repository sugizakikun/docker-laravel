<?php

namespace App\Http\Controllers\Auth;

use App\Http\Services\Auth\ResetPassword;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public function showResetForm(Request $request, ResetPassword $resetPassword)
    {
        $token = $request->route()->parameter('token');

        # トークン期限切れの場合/password/resetにリダイレクト
        if($resetPassword->hasExpired($request->email)){
            return redirect(route('password.request'));
        }

        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request, ResetPassword $resetPassword)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        $code = $request->all()['token'];
        $email = $request->all()['email'];
        $password = $request->all()['password'];

        $response = $resetPassword->execute($code, $email, $password);

        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }
}
