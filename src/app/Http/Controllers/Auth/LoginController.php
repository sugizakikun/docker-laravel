<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

//追加
use Ellaisys\Cognito\Auth\AuthenticatesUsers as CognitoAuthenticatesUsers;

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

    // use AuthenticatesUsers;
    use CognitoAuthenticatesUsers;

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
    }

    public function showLoginForm() {
        return view("auth.login");
    }

    /**
     * Authenticate User
     *
     * @throws \HttpException
     *
     * @return mixed
     */
    public function login(\Illuminate\Http\Request $request)
    {

        //Convert request to collection
        $collection = collect($request->all());

        //Authenticate with Cognito Package Trait (with 'web' as the auth guard)
        if ($response = $this->attemptLogin($collection, 'web')) {
            if ($response===true) {
                return redirect(route('home'))->with('success', true);
            } else if ($response===false) {
                // If the login attempt was unsuccessful you may increment the number of attempts
                // to login and redirect the user back to the login form. Of course, when this
                // user surpasses their maximum number of attempts they will get locked out.
                //
                //$this->incrementLoginAttempts($request);
                //
                //$this->sendFailedLoginResponse($collection, null);
            } else {
                return $response;
            } //End if
        } //End if

    } //Function ends
}
