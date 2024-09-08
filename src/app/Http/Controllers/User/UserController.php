<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Services\User\GetUser;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($userId, GetUser $getUser)
    {
        $user = $getUser->execute($userId);

        return view('user.show')->with('user', $user);
    }
}
