@extends('layouts.app')

@section('content')
    <!--プロフィール-->
    <div class="profile py-3">
        <x-profile_image :user="$user" size=150 />
        <p class="profile--name">{{$user->name}}</p>

        @if(auth()->user()->id != $user->id)
            <button class="btn btn-primary mb-2" data-toggle="modal" data-target="#followUserModal">follow</button>
        @endif
    </div>

    <!--ユーザーの最新投稿-->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @foreach($user->posts as $post)
                    <x-post :post="$post" :authUser="auth()->user()"></x-post>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal(follow) -->
    <x-form_action_modal
        modalId="followUserModal"
        submitButtonId="#followUserModalButton"
        route="{{ route('post.edit', ['postId' => $post->id]) }}"
        title="フォロー"
        buttonTitle="編集"
        method="POST"
    >
        <div class="modal-body">
            {{$user->name}} さんをフォローしますか？
        </div>
    </x-form_action_modal>
@endsection

<style lang="scss">
    .profile{
        text-align: center;

        .profile--name{
            font-size: 20px;
            font-weight: bold;
        }
    }

    .object-fit {
        object-fit: cover;
    }

    .thumbnail {
        object-fit: cover;
        border-radius: 50%;
    }

    .blur{
        -ms-filter: blur(6px);
        filter: blur(10px);
    }
</style>

