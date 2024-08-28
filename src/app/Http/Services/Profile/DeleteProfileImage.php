<?php

namespace App\Http\Services\Profile;

use Illuminate\Support\Facades\Auth;

class DeleteProfileImage
{
    public function execute()
    {
        $user = Auth::user();
        $user->profile_image_key = null;
        $user->save();
    }
}
