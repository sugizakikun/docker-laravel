<?php

namespace App\Http\Services\Profile;

use App\Util\NsfwApiClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Services\Common\ImageUploaderTrait;
use App\Http\Domains\Common\NsfwErrorResponseDomain;
use App\Http\Domains\Common\NsfwOutputResponseDomain;

class UpdateProfileImage
{
    use ImageUploaderTrait;

    /**
     * @var NsfwApiClient
     */
    protected $nsfwApiClient;

    /**
     * @param NsfwApiClient $nsfwApiClient
     */
    public function __construct(NsfwApiClient $nsfwApiClient)
    {
        $this->nsfwApiClient = $nsfwApiClient;
    }

    public function execute(UploadedFile $uploadedFile)
    {
        $storeImageOutput = $this->storeImage($uploadedFile);
        $nsfwApiResponse = $this->nsfwApiClient->singlePrediction($storeImageOutput['url']);

        # サーバーエラーの場合はアップロードされたS3オブジェクトを削除し早期リターン
        if(isset($nsfwApiResponse['error_code'])){
            Storage::delete($storeImageOutput['local_path']);
            $this->deleteUploadedImage($storeImageOutput['key']);

            return new NsfwErrorResponseDomain(
                $nsfwApiResponse['error_code'],
                $nsfwApiResponse['error_reason'],
                $nsfwApiResponse['url']
            );
        }

        # NSFWスコアが0.8以上の場合は早期リターン
        if( $nsfwApiResponse['score'] >= 0.8 ){
            Storage::delete($storeImageOutput['local_path']);
            $this->deleteUploadedImage($storeImageOutput['key']);

            return new NsfwOutputResponseDomain(
                $nsfwApiResponse['score'],
                $nsfwApiResponse['url']
            );
        }

        # NSFWスコアが0.8未満の場合は、UserテーブルにオブジェクトへのURLとキーを保存
        $user = Auth::user();
        $oldProfileImageKey = $user->profile_image_key;

        $user->profile_image_url = $storeImageOutput['url'];
        $user->profile_image_key = $storeImageOutput['key'];
        $user->save();

        # 更新前の画像はS3から削除
        if($oldProfileImageKey &&  $oldProfileImageKey !== $storeImageOutput['key']) {
            $this->deleteUploadedImage($oldProfileImageKey);
        }

        # S3への保存が成功したらWebサーバー上の一時ファイルを削除
        Storage::delete($storeImageOutput['local_path']);

        return new NsfwOutputResponseDomain(
            $nsfwApiResponse['score'],
            $nsfwApiResponse['url']
        );
    }
}
