<?php

namespace App\Http\Services\Profile;

use App\Util\NsfwApiClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Domains\Common\NsfwErrorResponseDomain;
use App\Http\Domains\Common\NsfwOutputResponseDomain;

class UpdateProfileImage
{
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
        $nsfwApiResponse = $this->nsfwApiClient->singlePrediction($storeImageOutput['s3_path']);

        # サーバーエラーの場合はアップロードされたS3オブジェクトを削除し早期リターン
        if(isset($nsfwApiResponse['error_code'])){
            Storage::delete($storeImageOutput['local_path']);
            $this->deleteUploadedImage($storeImageOutput['file_name']);

            return new NsfwErrorResponseDomain(
                $nsfwApiResponse['error_code'],
                $nsfwApiResponse['error_reason'],
                $nsfwApiResponse['url']
            );
        }

        # NSFWスコアが0.8以上の場合は早期リターン
        if( $nsfwApiResponse['score'] >= 0.8 ){
            Storage::delete($storeImageOutput['local_path']);
            $this->deleteUploadedImage($storeImageOutput['file_name']);

            return new NsfwOutputResponseDomain(
                $nsfwApiResponse['score'],
                $nsfwApiResponse['url']
            );
        }

        # NSFWスコアが0.8未満の場合は、UserテーブルにオブジェクトへのURLとキーを保存
        $user = Auth::user();
        $oldProfileImageKey = $user->profile_image_key;

        $user->profile_image_url = $storeImageOutput['s3_path'];
        $user->profile_image_key = $storeImageOutput['file_name'];
        $user->save();

        # 更新前の画像はS3から削除
        if($oldProfileImageKey &&  $oldProfileImageKey !== $storeImageOutput['file_name']) {
            $this->deleteUploadedImage($oldProfileImageKey);
        }

        # S3への保存が成功したらWebサーバー上の一時ファイルを削除
        Storage::delete($storeImageOutput['local_path']);

        return new NsfwOutputResponseDomain(
            $nsfwApiResponse['score'],
            $nsfwApiResponse['url']
        );
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return array
     */
    public function storeImage(UploadedFile $uploadedFile):array
    {
        $path = $uploadedFile->store('public/img');
        $fileContents = Storage::get($path);

        $randomStr = base_convert(md5(uniqid()), 16,36);
        $ext = $uploadedFile->guessExtension();
        $fileName = "$randomStr.$ext";

        Storage::disk('s3')->put($fileName, $fileContents);

        return [
            's3_path'  => Storage::disk('s3')->url($fileName),
            'file_name' => $fileName,
            'local_path' => $path,
        ];
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
