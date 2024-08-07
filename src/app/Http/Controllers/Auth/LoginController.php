<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;  // 追加
use App\Models\User;  // 追加
use Illuminate\Support\Facades\Auth;  // 追加


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
    protected $redirectTo = '/home';

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

     /**
     * @param Request
     * @param User $user
     * @return User
     */
    protected function authenticated(Request $request, $user)
    {
        return $user;
    }

    /**
     * @param Request $request
     */
    protected function loggedOut(Request $request)
    {
        Auth::logout();
        $request->session()->regenerate();

        return response()->json();
    }
}
