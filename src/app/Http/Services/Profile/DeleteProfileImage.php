<?php

namespace App\Http\Services\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeleteProfileImage
{
    public function execute()
    {
        $user = Auth::user();
        $profileImageKey = $user->profile_image_key;

        $user->profile_image_url = null;
        $user->profile_image_key = null;
        $user->save();

        Storage::disk('s3')->delete($profileImageKey);
    }
}
