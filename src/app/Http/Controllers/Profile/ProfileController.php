<?php

namespace App\Http\Controllers\Profile;

use App\Http\Services\Profile\UpdateProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile')->with('user', $user);
    }

    /**
     * @param Request $request
     * @param UpdateProfile $updateProfile
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, UpdateProfile $updateProfile)
    {
        $data = $request->all();

        $name = $data['name'];
        $email = $data['email'];

        $result = $updateProfile->execute($name, $email);

        return redirect('/profile')->with('result', $result);
    }
}
