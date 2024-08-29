<?php

namespace App\Http\Services\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateProfileImage
{
    /**
     * @param string|null $path
     * @return void
     */
    public function execute(?string $path)
    {
        $randomStr = base_convert(md5(uniqid()), 16,36);
        $fileName = $randomStr.'.png';

        $fileContents = Storage::get($path);
        Storage::disk('s3')->put($fileName, $fileContents);
        $s3Path = Storage::disk('s3')->url($fileName);

        $user = Auth::user();
        $oldProfileImageKey = $user->profile_image_key;

        $user->profile_image_url = $s3Path;
        $user->profile_image_key = $fileName;
        $user->save();

        if($oldProfileImageKey !== $fileName) {
            $this->deleteUploadedImage($oldProfileImageKey);
        }

        # S3への保存が成功したらWebサーバー上の一時ファイルを削除
        Storage::delete($path);
    }

    /**
     * @param $oldImageKey
     * @return void
     */
    public function deleteUploadedImage($oldImageKey) :void
    {
        Storage::disk('s3')->delete($oldImageKey);
    }
}
