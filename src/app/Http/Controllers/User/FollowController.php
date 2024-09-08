<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Services\User\CreateFollow;
use App\Http\Services\User\DeleteFollow;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param $userId
     * @param CreateFollow $createFollow
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create($userId, CreateFollow $createFollow)
    {
        $follow = $createFollow->execute(Auth::id(), $userId);

        return redirect( route('user.show', ['userId'=> $userId]) )
            ->with(['result' => $follow->follower->name.' さんと友達になりました！']);
    }

    /**
     * @param $userId
     * @param DeleteFollow $deleteFollow
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($userId,  DeleteFollow $deleteFollow)
    {
        $follow = $deleteFollow->execute($userId, Auth::id());

        return redirect( route('user.show', ['userId'=> $userId]) )
            ->with([
                'bgColor' => 'danger',
                'result' => $follow->follower->name.'さんのフォロー解除しました。'
            ]);
    }
}
