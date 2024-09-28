<?php

namespace App\Http\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateGoogleUser
{
    public function execute(array $getUserInfoResponse)
    {
        return (new User())->newQuery()
            ->firstOrCreate([
                'email' => $getUserInfoResponse['email'],
                'cognito_username' => $getUserInfoResponse['username']
            ]);
    }
}