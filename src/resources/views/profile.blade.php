@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ __('Dashboard') }}
                </div>

                <div class="card-body">
                    <img src="{{ $user->profile_image_key ? asset($user->profile_image_key): asset('img/profile_female.png') }}" width="75" height="75">
                    Welocome! {{$user->name}} san!

                    <h3>Email address</h3>
                    <p>{{$user->email}}</p>

                    <div class="mb-3 justify-content-center">
                        <a href="#">
                            <button class="btn btn-primary col-12">
                                <i class="fa fa-pen"></i>
                                プロフィールを編集する
                            </button>
                        </a>
                    </div>

                    <div class="mb-3 justify-content-center">
                        <form method="POST" action="{{ route('profile.edit') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            <input class="form-control" type="file" name="image" id="imageInput" aria-describedby="fileHelp" aria-required="true">
                            <div id="fileHelp" class="form-text">許可されるファイル形式: JPG, PNG, GIF, WebP, AVIF (最大1MB)</div>

                            <button class="btn btn-primary mt-3 col-12" id="uploadButton" disabled>アップロード</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('imageInput').addEventListener('change', function() {
            const uploadButton = document.getElementById('uploadButton');
            if (this.files && this.files.length > 0) {
                const file = this.files[0];
                const maxSize = 1024 * 1024; // 10MB
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif'];

                if (!allowedTypes.includes(file.type)) {
                    alert('許可されていないファイル形式です。JPG、PNG、GIF、WebP、AVIF形式のファイルを選択してください。');
                    this.value = '';
                    return;
                }

                if (file.size > maxSize) {
                    alert('ファイルサイズが大きすぎます。10MB以下のファイルを選択してください。');
                    this.value = '';
                    return;
                }

                uploadButton.disabled = false;
            } else {
                uploadButton.disabled = true;  // Keep the button disabled
            }
        });
    });
</script>
