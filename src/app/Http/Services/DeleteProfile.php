<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;

class DeleteProfile
{
    public function execute()
    {
        $user = Auth::user();
        $user->profile_image_key = null;
        $user->save();
    }
}
