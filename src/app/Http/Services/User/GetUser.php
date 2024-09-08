<?php

namespace App\Http\Services\User;

use App\Models\User;

class GetUser
{
    /**
     * @param $userId
     * @return User
     */
    public function execute($userId) : User
    {
        return (new User())
            ->newQuery()
            ->with(['posts' => function ($query) {
                $query->with(['postImages'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5);
            }])
            ->where('id', $userId)
            ->firstOrFail();
    }
}
