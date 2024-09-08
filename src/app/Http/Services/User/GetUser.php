<?php

namespace App\Http\Services\User;

use App\Models\User;

class GetUser
{
    /**
     * @param $userId
     * @param int $authUserId
     * @return User
     */
    public function execute($userId, int $authUserId) : User
    {
        return (new User())
            ->newQuery()
            ->with(['posts' => function ($query) {
                $query->with(['postImages'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5);
            }])
            ->withCount(['followed as isFollowing' => function ($query) use ($authUserId){
                $query->where('following', $authUserId);
            }])
            ->where('id', $userId)
            ->firstOrFail();
    }
}
