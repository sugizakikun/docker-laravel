<?php

namespace App\Http\Services\Posts;

use App\Models\Post;

class DeletePost
{
    public function execute(int $postId)
    {
        (new Post())
            ->where('id', $postId)
            ->first()
            ->delete();
    }
}
