@extends('layouts.app')

@section('content')
    <!--アラート-->
    @if(session('result'))
        <x-alert :message="session('result')" :color="session('bgColor') ?? 'success'"/>
    @endif

    <!--プロフィール-->
    <div class="profile py-3">
        <x-profile_image :user="$user" size=150 />
        <p class="profile--name">{{$user->name}}</p>

        @if(auth()->user()->id != $user->id)
            <div class="follow--btn">
                @if(!$user->isFollowing)
                    <button class="btn btn-primary mb-2" id="#followUserButton" data-toggle="modal" data-target="#followUserModal">follow</button>
                @else
                    <button class="btn btn-outline-primary mb-2" id="#unfollowUserButton" data-toggle="modal" data-target="#unfollowUserModal">following</button>
                @endif
            </div>
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

    <!-- Modal(フォロー) -->
    <x-form_action_modal
        modalId="followUserModal"
        submitButtonId="#followUserModalButton"
        route="{{ route('follow', ['userId' => $user->id]) }}"
        title="フォロー"
        buttonTitle="フォローする"
        method="PUT"
    >
        <div class="modal-body">
            {{$user->name}} さんをフォローしますか？
        </div>
    </x-form_action_modal>

    <!-- Modal(フォロー解除) -->
    <x-form_action_modal
        modalId="unfollowUserModal"
        submitButtonId="#unfollowUserModalButton"
        route="{{ route('follow', ['userId' => $user->id]) }}"
        title="フォロー解除"
        buttonTitle="解除する"
        method="DELETE"
    >
        <div class="modal-body">
            {{$user->name}} さんのフォローを解除しますか？
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

