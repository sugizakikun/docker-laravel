<?php

namespace App\Http\Services\Posts;

use App\Models\Post;
use App\Models\PostImage;
use App\Util\NsfwApiClient;
use Illuminate\Support\Facades\Storage;

class CreatePost
{
    public function __construct(NsfwApiClient $nsfwApiClient)
    {
        $this->nsfwApiClient = $nsfwApiClient;
    }

    public function execute(int $userId, string $content, array $images)
    {
        $post = $this->createPost($userId, $content);

        if(count($images) > 0){
            # Webサーバーに画像データを一時保管する
            $files= $this->storeImages($images);

            # Webサーバーに保存した画像データをS3にアップロードする
            $s3Paths = $this->uploadImagesIntoS3($files);

            # 複数の画像に対してnsfwスコアを付与する
            $nsfwPredictions = $this->nsfwApiClient->batchPrediction($s3Paths);

            # nsfwの結果による画像の振り分けを行う
            $this->processPredictedImages($post->id, $nsfwPredictions);
        }
    }

    /**
     * @param int $userId
     * @param string $content
     * @return Post
     */
    private function createPost(int $userId, string $content):Post
    {
        return (new Post)->newQuery()
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

    /**
     * @param int $postId
     * @param array $nsfwPredictions
     * @return void
     */
    private function processPredictedImages(int $postId, array $nsfwPredictions):void
    {
        foreach ($nsfwPredictions as $prediction) {
            if(isset($prediction['error_code'])) {
                Storage::disk('s3')->delete($prediction['key']);
                continue;
            }

            if($prediction['score'] > 0.8) {
                Storage::disk('s3')->delete($prediction['key']);
                continue;
            }

            $this->createPostImages($postId, $prediction);
        }
    }

    /**
     * @param int $postId
     * @param array $image
     * @return PostImage
     */
    private function createPostImages(int $postId, array $image): PostImage
    {
        return (new PostImage())
            ->newQuery()
            ->create([
                'post_id' => $postId,
                'image_url' => $image['url'],
                'image_key' => $image['key'],
                'nsfw_score' => $image['score']
            ]);
    }
}
