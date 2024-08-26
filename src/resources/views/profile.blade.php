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
                    <div class="position-relative mb-3">
                        <img src="{{ $user->profile_image_key ? asset($user->profile_image_key): asset('img/profile_female.png') }}" width="200" height="200">
                        <div class="position-absolute color-bg-defaultcolor-fg-default px-2 py-1 left-0 bottom-0 ml-2 mb-2">
                            <button class="btn btn-light" data-toggle="modal" data-target="#updateProfileImage">Edit</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="basic-url" class="form-label">Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="name" value="{{$user->name}}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="basic-url" class="form-label">Email Address</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="email" value="{{$user->email}}">
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="updateProfileImage" tabindex="-1" role="dialog" aria-labelledby="updateProfileImage" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('profile.edit') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalCenterTitle">プロフィール画像の変更</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="_method" value="PUT">
                                        <input class="form-control" type="file" name="image" id="imageInput" aria-describedby="fileHelp" aria-required="true">
                                        <div id="fileHelp" class="form-text">許可されるファイル形式: JPG, PNG, GIF, WebP, AVIF (最大1MB)</div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                                        <button type="submit" class="btn btn-primary" id="uploadButton" disabled>アップロード</button>
                                    </div>
                                </form>
                            </div>
                        </div>
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

            console.log(uploadButton, this.files);
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
