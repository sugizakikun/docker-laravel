<?php

namespace App\Http\Services\Profile;

use App\Util\HttpClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateProfileImage
{
    /**
     * @var HttpClient
     */
    public $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string|null $path
     * @return float
     */
    public function execute(?string $path) :float
    {
        $randomStr = base_convert(md5(uniqid()), 16,36);
        $fileName = $randomStr.'.png';

        $fileContents = Storage::get($path);
        Storage::disk('s3')->put($fileName, $fileContents);
        $s3Path = Storage::disk('s3')->url($fileName);

        $nsfwScore = $this->calculateNsfwScore($s3Path);

        # サーバーエラーの場合はアップロードされたS3オブジェクトを削除し早期リターン
        if($nsfwScore === 999.999){
            $this->deleteUploadedImage($s3Path);
            return $nsfwScore;
        }

        # NSFWスコアが0.8以上の場合は早期リターン
        if($nsfwScore >= 0.8 ){
            $this->deleteUploadedImage($s3Path);
            return $nsfwScore;
        }

        # NSFWスコアが0.8未満の場合は、UserテーブルにオブジェクトへのURLとキーを保存
        $user = Auth::user();
        $oldProfileImageKey = $user->profile_image_key;

        $user->profile_image_url = $s3Path;
        $user->profile_image_key = $fileName;
        $user->save();

        # 更新前の画像はS3から削除
        if($oldProfileImageKey &&  $oldProfileImageKey !== $fileName) {
            $this->deleteUploadedImage($oldProfileImageKey);
        }

        # S3への保存が成功したらWebサーバー上の一時ファイルを削除
        Storage::delete($path);

        return $nsfwScore;
    }


    /**
     * @param $s3Path
     * @return float
     */
    public function calculateNsfwScore($s3Path) :float
    {
        $prefix = 'http://'.config('fargate.task_ip_address').':5000';
        $suffix = '/?url='. urlencode($s3Path);
        $endPoint = $prefix.$suffix;

        $jsonString = $this->httpClient->get($endPoint);
        $jsonArray = json_decode($jsonString, true);

        if(isset($jsonArray['error_code'])){
            return 999.999;
        }

        return  $jsonArray['score'];
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
