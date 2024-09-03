<?php

namespace App\Http\Controllers\Profile;

use App\Http\Domains\Common\NsfwOutputResponseDomain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Domains\Common\NsfwErrorResponseDomain;
use App\Http\Services\Profile\DeleteProfileImage;
use App\Http\Services\Profile\UpdateProfileImage;

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
        $nsfwResponseArray =  $nsfwResponse->toArray();

        // NSFW応答の処理
        $redirectData = [
            'bgColor' => $nsfwResponseArray['alertBgColor'],
            'result' => ($nsfwResponse instanceof NsfwErrorResponseDomain)
                ? $nsfwResponseArray['code'] . ' error: ' . $nsfwResponseArray['message']
                : $nsfwResponseArray['message']
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
