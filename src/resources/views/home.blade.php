@extends('layouts.app')

@section('content')

@if(session('result'))
    <x-alert :message="session('result')" :color="session('bgColor') ?? 'success'"/>
@endif

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @foreach($posts as $post)
                <x-post :post="$post"></x-post>
            @endforeach
        </div>
    </div>
</div>

<!--新規投稿ボタン-->
<button class="fixed-button" data-toggle="modal" data-target="#createPostModal">+</button>

<!-- Modal(updatePost) -->
<x-form_action_modal
    modalId="createPostModal"
    submitButtonId="createPostModalButton"
    route="{{ route('post.create')}}"
    title="新規投稿"
    buttonTitle="投稿する"
    method="POST"
>
    <!-- モーダルのボディ部分 -->
    <div class="modal-body">
        <div class="form-group">
            <label for="content">今のあなたの気持ちは？</label>
            <textarea
                class="form-control"
                rows="10"
                id="content"
                name="content"
            ></textarea>
        </div>
        <div class="form-group">
            <label for="photo">画像ファイル（複数可）:</label>
            <input type="file" class="form-control" name="images[]" multiple aria-describedby="fileHelp">
            <div id="fileHelp" class="form-text">許可されるファイル形式: JPG, PNG, GIF, WebP, AVIF (最大1MB)</div>
        </div>
    </div>
</x-form_action_modal>

@endsection

<style>
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

    .fixed-button {
        position: fixed; /* スクロールしても位置を固定 */
        bottom: 20px; /* 画面の下から20px */
        right: 20px; /* 画面の右から20px */
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000; /* 他の要素の上に表示 */
    }
</style>

