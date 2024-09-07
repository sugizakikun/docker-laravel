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

    public function execute(int $userId, string $content, array $uploadedFiles)
    {
        $post = $this->createPost($userId, $content);

        if(count($uploadedFiles) > 0){
            # Webサーバーに画像データを一時保管する
            $storeImageOutPuts = $this->batchStoreImages($uploadedFiles);

            # 複数の画像に対してnsfwスコアを付与する
            $nsfwPredictions = $this->nsfwApiClient->batchPrediction($storeImageOutPuts );

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
     * @param array $uploadFiles
     * @return array
     */
    private function batchStoreImages(array $uploadedFiles):array
    {
        $files = [];

        foreach ($uploadedFiles as $uploadedFile) {
            $path = $uploadedFile->store('public/img');
            $fileContents = Storage::get($path);

            $randomStr = base_convert(md5(uniqid()), 16,36);
            $ext = $uploadedFile->guessExtension();
            $fileName = "$randomStr.$ext";

            Storage::disk('s3')->put($fileName, $fileContents);

            $files[] = [
                'url'  => Storage::disk('s3')->url($fileName),
                'key' => $fileName,
                'local_path' => $path,
            ];
        }

        return $files;
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
