<?php

namespace App\Http\Controllers\Post;

use App\Http\Services\Posts\CreatePost;
use App\Http\Services\Posts\DeletePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Services\Posts\UpdatePost;

class PostController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @param CreatePost $createPost
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request, CreatePost $createPost)
    {
        $userId = Auth::id();

        $data = $request->all();
        $content = $data['content'];
        $images = $request->file('images');

        $createPost->execute($userId, $content, $images);

        return redirect('home')->with(['result' => 'Post has been created successfully!']);
    }

    /**
     * @param Request $request
     * @param UpdatePost $updatePost
     * @param $postId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, UpdatePost $updatePost, $postId)
    {
        $data = $request->all();
        $content = $data['content'];

        $updatePost->execute((int)$postId, $content);

        return redirect('home')->with(['result' => 'Post has been edited successfully!']);
    }

    /**
     * @param DeletePost $deletePost
     * @param $postId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(DeletePost $deletePost, $postId)
    {
        $deletePost->execute((int)$postId);

        return redirect('home')->with(['result' => 'Post has been deleted successfully!']);
    }
}
