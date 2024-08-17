<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// 追加
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm() {
        $params = [
            '{domain_name}' => config('cognito.domain_name'),
            '{region}' => config('cognito.region'),
            '{app_client_id}' => config('cognito.app_client_id'),
            '{redirect_url}' => 'http://localhost:8080/home'
        ];

        $authURL = str_replace(array_keys($params), array_values($params), config('cognito.google_auth_url') );

        return view("auth.login")->with(['authUrl' => $authURL ]);
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        try
        {
            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }
        }
        catch(CognitoIdentityProviderException $c) {
            return $this->sendFailedCognitoResponse($c);
        }
        catch (\Exception $e) {
            return $this->sendFailedLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    private function sendFailedCognitoResponse(CognitoIdentityProviderException $exception)
    {
        throw ValidationException::withMessages([
            $this->username() => $exception->getAwsErrorMessage(),
        ]);
    }
}
