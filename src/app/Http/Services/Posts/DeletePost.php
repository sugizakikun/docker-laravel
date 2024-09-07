<?php

namespace App\Http\Services\Posts;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeletePost
{
    /**
     * @param int $postId
     * @return void
     */
    public function execute(int $postId)
    {
        DB::transaction(function () use ($postId) {
            $this->deletePostImage($postId);

            $this->deletePost($postId);
        });
    }

    /**
     * @param int $postId
     * @return void
     */
    private function deletePostImage(int $postId):void
    {
        $postImages = (new PostImage())
            ->where('post_id', $postId)
            ->get();

        foreach ($postImages as $postImage) {
            Storage::disk('s3')->delete($postImage->image_key);
        }

        $postImages->each->delete();
    }

    /**
     * @param int $postId
     * @return bool
     */
    private function deletePost(int $postId): bool
    {
        return (new Post())
            ->where('id', $postId)
            ->first()
            ->delete();
    }
}
