<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UpdateProfile
{
    public function execute(?string $path)
    {
        $user = Auth::user();

        $user->profile_image_key = $path;
        $user->save();
    }
}
