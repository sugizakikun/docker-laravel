<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Services\DestroyUser;

class WithdrawController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('completed');
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
            return redirect()->route('withdraw.completed');
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function completed()
    {
        return view('withdrawal_completed');
    }
}
