<?php

namespace App\Http\Services\User;

use App\Models\Follow;

class DeleteFollow
{
    /**
     * @param $userId
     * @param $authUserId
     * @return Follow|null
     */
    public function execute($userId, $authUserId) : ?Follow
    {
        $follow = (new Follow())
            ->newQuery()
            ->where('following', $authUserId)
            ->where('followed', $userId)
            ->firstOrFail();

        if($follow){
            $follow->delete();
        }

        return $follow;
    }
}
