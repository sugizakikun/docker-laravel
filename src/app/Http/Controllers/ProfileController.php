<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Services\UpdateProfile;

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

    public function update(Request $request, UpdateProfile $updateProfile)
    {
        // ディレクトリ名を任意の名前で設定します
        $dir = 'img';
        $path = $request->file('image')->store('public/' . $dir);

        $updateProfile->execute($path);

        // ページを更新します
        return redirect('/profile');
    }
}
