<?php

namespace App\Http\Services\Profile;

use Illuminate\Support\Facades\Auth;

class UpdateProfile
{
    public function execute(?string $name, string $email)
    {
        $user = Auth::user();

        $user->email = $email;
        $user->name = $name;

        $user->save();
    }
}
