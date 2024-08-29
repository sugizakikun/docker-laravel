<?php

namespace App\Http\Services\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateProfileImage
{
    public function execute(?string $path)
    {
        $randomStr = base_convert(md5(uniqid()), 16,36);
        $fileName = $randomStr.'.png';

        $fileContents = Storage::get($path);
        Storage::disk('s3')->put($fileName, $fileContents);
        $s3Path = Storage::disk('s3')->url($fileName);

        $user = Auth::user();
        $user->profile_image_url = $s3Path;
        $user->profile_image_key = $fileName;
        $user->save();

        # S3への保存が成功したらWebサーバー上の一時ファイルを削除
        Storage::delete($path);
    }
}
