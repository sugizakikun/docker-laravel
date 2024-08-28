<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Services\DeleteProfile;
use App\Http\Services\UpdateProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     */
    public function update(Request $request, UpdateProfile $updateProfile)
    {
        if(array_key_exists("image", $request->all())){
            $path = $request->file('image')->store('public/img');

            $updateProfile->execute($path);
        }

        // ページを更新します
        return redirect('/profile');
    }

    /**
     * @param DeleteProfile $deleteProfile
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(DeleteProfile $deleteProfile)
    {
        $deleteProfile->execute();
        // ページを更新します
        return redirect('/profile');
    }
}
