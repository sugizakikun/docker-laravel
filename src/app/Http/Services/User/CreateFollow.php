<?php

namespace App\Http\Services\User;

use App\Models\Follow;

class CreateFollow
{
    /**
     * @param $following
     * @param $followed
     * @return Follow
     */
    public function execute($following, $followed): Follow
    {
        return (new Follow())
            ->newQuery()
            ->create([
                'following' => $following,
                'followed' => $followed,
            ]);
    }
}
