<?php

namespace App\Http\Services\Posts;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class CreatePost
{
    public function execute(int $userId, string $content, array $images)
    {

        if(count($images) > 0){
            # Webサーバーに画像データを一時保管する
            $files= $this->storeImages($images);

            # Webサーバーに保存した画像データをS3にアップロードする
            $s3Paths = $this->uploadImagesIntoS3($files);

            
        }

        $this->createPost($userId, $content);

    }

    /**
     * @param int $userId
     * @param string $content
     * @return void
     */
    private function createPost(int $userId, string $content):void
    {
        (new Post)->newQuery()
            ->create([
                'author_id' => $userId,
                'content' => $content
            ]);
    }

    /**
     * @param array $images
     * @return array
     */
    private function storeImages(array $images):array
    {
        $files = [];

        foreach ($images as $image) {
            $randomStr = base_convert(md5(uniqid()), 16,36);
            $ext = $image->guessExtension();

            $filename = "$randomStr.$ext";
            $files[] = [
                'name' => $filename,
                'contents' =>  $image->storeAs('public/img', $filename)
            ];
        }

        return $files;
    }

    /**
     * @param array $files
     * @return array
     */
    private function uploadImagesIntoS3(array $files):array
    {
        $s3Paths = [];

        foreach ($files as $file) {
            $fileContent = Storage::get($file['contents']);
            Storage::disk('s3')->put($file['name'], $fileContent);
            $s3Paths[] = [
                'key'  => $file['name'],
                'url' => Storage::disk('s3')->url($file['name'])
            ];
        }

        return $s3Paths;
    }
}
