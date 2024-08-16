<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\DB;
use App\Cognito\CognitoClient;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\PasswordReset;

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

    private $cognitoClient;

    public function __construct(CognitoClient $cognitoClient)
    {
        $this->cognitoClient = $cognitoClient;
    }

    public function showResetForm(Request $request)
    {
        $token = $request->route()->parameter('token');

        # トークン期限切れの場合/password/resetにリダイレクト
        if($this->hasExpired($request->email)){
            return redirect(route('password.request'));
        }

        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
    {
        DB::beginTransaction();
        $request->validate($this->rules(), $this->validationErrorMessages());

        $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        $code = $request->all()['token'];
        $email = $request->all()['email'];
        $password = $request->all()['password'];

        $response = $this->cognitoClient->resetPassword($code, $email, $password);

        DB::commit();
        // ここでConfirmForgotPassword API()呼び出し
        // 参考リンク：https://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_ConfirmForgotPassword.html
        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    private function hasExpired(string $email)
    {
        $passwordReset = PasswordReset::where('email', $email)->first();

        $createdAt = new Carbon($passwordReset->created_at);

        return $createdAt->subMinutes(10)->isPast();
    }
}
