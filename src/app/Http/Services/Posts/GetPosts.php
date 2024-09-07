<?php

namespace App\Http\Services\Posts;

use App\Models\Post;

class GetPosts
{
    public function execute()
    {
        return (new Post())
            ->with([
                'author',
                'postImages'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
