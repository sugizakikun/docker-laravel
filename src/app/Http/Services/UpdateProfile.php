<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateProfile
{
    public function execute(?string $path)
    {

        $fileContents = Storage::get($path);

        Storage::disk('s3')->put('test2.png', $fileContents);
        $path = Storage::disk('s3')->temporaryUrl('test2.png', now()->addMinutes(5));

        $user = Auth::user();
        $user->profile_image_key = $path;
        $user->save();
    }
}
