<?php

namespace App\Http\Services\Profile;

use App\Models\User;
use App\Util\NsfwApiClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Services\Common\ImageUploaderTrait;
use App\Http\Domains\NsfwApi\NsfwErrorResponseDomain;
use App\Http\Domains\NsfwApi\NsfwOutputResponseDomain;

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

    public function execute(UploadedFile $uploadedFile, User $user)
    {
        $storeImageOutput = $this->storeImage($uploadedFile, $user);
        $nsfwApiResponse = $this->nsfwApiClient->singlePrediction($storeImageOutput['url']);

        #Webサーバー上の一時ファイルを削除
        Storage::delete($storeImageOutput['local_path']);

        # サーバーエラーの場合はアップロードされたS3オブジェクトを削除し早期リターン
        if(isset($nsfwApiResponse['error_code'])){
            $this->deleteUploadedImage($storeImageOutput['key']);

            return new NsfwErrorResponseDomain(
                $nsfwApiResponse['error_code'],
                $nsfwApiResponse['error_reason'],
                $nsfwApiResponse['url']
            );
        }

        # NSFWスコアが0.8以上の場合はタグを付与した上で早期リターン
        if( $nsfwApiResponse['score'] >= 0.8 ){
            Storage::disk('s3')->put(
                $storeImageOutput['key'], 
                $storeImageOutput['file_contents'], 
                ['Tagging' => ['is_erotic'=>'1']] 
            );
            
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

        return new NsfwOutputResponseDomain(
            $nsfwApiResponse['score'],
            $nsfwApiResponse['url']
        );
    }
}
