<?php

namespace App\Http\Services\Profile;

use App\Util\HttpClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Domains\Common\NsfwErrorResponseDomain;
use App\Http\Domains\Common\NsfwOutputResponseDomain;

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
     * @return NsfwErrorResponseDomain|NsfwOutputResponseDomain
     */
    public function execute(?string $path)
    {
        $randomStr = base_convert(md5(uniqid()), 16,36);
        $fileName = $randomStr.'.png';

        $fileContents = Storage::get($path);
        Storage::disk('s3')->put($fileName, $fileContents);
        $s3Path = Storage::disk('s3')->url($fileName);

        $nsfwApiResponse = $this->sendNsfwApiRequest($s3Path);

        # サーバーエラーの場合はアップロードされたS3オブジェクトを削除し早期リターン
        if(isset($nsfwApiResponse['error_code'])){
            $this->deleteUploadedImage($fileName);

            return new NsfwErrorResponseDomain(
                $nsfwApiResponse['error_code'],
                $nsfwApiResponse['error_reason'],
                $nsfwApiResponse['url']
            );
        }

        # NSFWスコアが0.8以上の場合は早期リターン
        if( $nsfwApiResponse['score'] >= 0.8 ){
            $this->deleteUploadedImage($fileName);

            return new NsfwOutputResponseDomain(
                $nsfwApiResponse['score'],
                $nsfwApiResponse['url']
            );
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

        return new NsfwOutputResponseDomain(
            $nsfwApiResponse['score'],
            $nsfwApiResponse['url']
        );
    }


    /**
     * @param string $s3Path
     * @return array
     */
    public function sendNsfwApiRequest(string $s3Path) :array
    {
        $prefix = 'http://'.config('fargate.task_ip_address').':5000';
        $suffix = '/?url='. urlencode($s3Path);
        $endPoint = $prefix.$suffix;

        $jsonString = $this->httpClient->get($endPoint);
        return  json_decode($jsonString, true);
    }

    /**
     * @param string $oldImageKey
     * @return void
     */
    public function deleteUploadedImage(string $oldImageKey) :void
    {
        Storage::disk('s3')->delete($oldImageKey);
    }
}
