<?php

namespace App\Http\Services\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateProfileImage
{
    public function execute(?string $path)
    {

        $fileContents = Storage::get($path);

        Storage::disk('s3')->put('test2.png', $fileContents);
        $s3Path = Storage::disk('s3')->temporaryUrl('test2.png', now()->addMinutes(5));

        $user = Auth::user();
        $user->profile_image_key = $s3Path;
        $user->save();

        # S3への保存が成功したらWebサーバー上の一時ファイルを削除
        Storage::delete($path);
    }
}
