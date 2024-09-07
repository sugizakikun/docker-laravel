<?php

namespace App\Http\Services\Posts;

use App\Models\Post;

class UpdatePost
{
    public function execute(int $postId, string $content)
    {
        (new Post())
            ->where('id', $postId)
            ->update(['content' => $content]);
    }
}
