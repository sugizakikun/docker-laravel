<?php

namespace App\Http\Controllers;

use App\Http\Services\Posts\GetPosts;
use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(GetPosts $getPosts)
    {
        $posts = $getPosts->execute();

        return view('home')->with('posts', $posts);
    }
}
