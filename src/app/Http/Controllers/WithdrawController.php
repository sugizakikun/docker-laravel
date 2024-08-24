<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cognito\CognitoClient;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\DestroyUser;

class WithdrawController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CognitoClient $cognitoClient)
    {
        $this->middleware('auth');
        $this->cognitoClient = $cognitoClient;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('withdraw');
    }

    /**
     * @param DestroyUser $destroyUser
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function destroy(DestroyUser $destroyUser)
    {
        $user = Auth::user();
        $isSucceeded = $destroyUser->execute($user);

        if($isSucceeded){
            return redirect()->route('login');
        }
    }
}
