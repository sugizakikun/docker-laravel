<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Domains\Common\NsfwErrorResponseDomain;
use App\Http\Domains\Common\NsfwOutputResponseDomain;
use App\Http\Services\Profile\DeleteProfileImage;
use App\Http\Services\Profile\UpdateProfileImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileImageController extends Controller
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
     * @param Request $request
     * @param UpdateProfileImage $updateProfileImage
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void|null
     */
    public function update(Request $request, UpdateProfileImage $updateProfileImage)
    {
        if(!array_key_exists("image", $request->all())){
            return null;
        }

        $path = $request->file('image')->store('public/img');
        $nsfwResponse = $updateProfileImage->execute($path);

        // NSFW応答の処理
        $redirectData = [
            'bgColor' => $nsfwResponse['alertBgColor'],
            'result' => ($nsfwResponse instanceof NsfwErrorResponseDomain)
                ? $nsfwResponse['code'] . ':' . $nsfwResponse['message']
                : $nsfwResponse['message']
        ];

        return redirect('/profile')->with($redirectData);
    }

    /**
     * @param DeleteProfileImage $deleteProfileImage
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(DeleteProfileImage $deleteProfileImage)
    {
        $deleteProfileImage->execute();
        // ページを更新します
        return redirect('/profile')->with('result', 'Profile image has been reset successfully');
    }
}
