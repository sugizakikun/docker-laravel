@extends('layouts.app')

@section('content')

@if(session('result'))
    <x-alert :message="session('result')" color="success"/>
@endif

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="mb-5">
                <h2 class="text-dark border-bottom">Profile</h2>
                <div class="position-relative mb-3">
                    <x-profile_image :user="$user" size=300 />
                    <div class="dropdown position-absolute color-bg-defaultcolor-fg-default px-2 py-1 left-0 bottom-0 ml-2 mb-2">
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Edit
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button class="dropdown-item" data-toggle="modal" data-target="#updateProfileImageModal" id="updateProfileImageButton">Upload Image</button>
                            <button class="dropdown-item" data-toggle="modal" data-target="#removeProfileImageModal" id="removeProfileImageButton">Remove Image</button>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.edit') }}">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="name" name="name" value="{{$user->name}}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="email" name="email" value="{{$user->email}}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>

            <div>
                <h2 class="text-danger border-bottom">Delete Account</h2>
                <div class="mb-3">
                    <p>Once you delete your account, there is no going back. Please be certain.</p>
                    <button class="btn btn-danger" data-toggle="modal" data-target="#deleteProfileModal">Delete your account</button>
                </div>
            </div>

            <!-- Modal(updateProfileImage) -->
            <div class="modal fade" id="updateProfileImageModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileImageModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('profile_image.edit') }}" enctype="multipart/form-data">
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
                                <button type="submit" class="btn btn-primary" id="uploadProfileImageButton" disabled>アップロード</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal(removeProfileImage) -->
            <div class="modal fade" id="removeProfileImageModal" tabindex="-1" role="dialog" aria-labelledby="removeProfileImageModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('profile_image.delete') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalCenterTitle">プロフィール画像の削除</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="_method" value="DELETE">
                                プロフィール画像をリセットしてもよろしいですか？
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                                <button type="submit" class="btn btn-danger" id="removeProfileImageButton">リセットする</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal(deleteAccount) -->
            <div class="modal fade" id="deleteProfileModal" tabindex="-1" role="dialog" aria-labelledby="deleteProfileModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('profile.destroy') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalCenterTitle">アカウントの削除</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <div>
                                    This is extremely important.
                                </div>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="_method" value="DELETE">
                                本当に退会しますか？

                                <hr>
                                <div class="mb-3">
                                    <label for="confirm-email" class="form-label">Your email:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="confirm-email">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm-phrase" class="form-label">To verify, "delete my account" type below:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="confirm-phrase">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                                <button type="submit" class="btn btn-danger" id="deleteProfileButton" disabled>退会する</button>
                            </div>
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
            const uploadButton = document.getElementById('uploadProfileImageButton');

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

    document.addEventListener('DOMContentLoaded', function () {
        const emailInput = document.getElementById('confirm-email');
        const phraseInput = document.getElementById('confirm-phrase');
        const deleteProfileButton = document.getElementById('deleteProfileButton');

        // ユーザーのメールアドレスをサーバーから取得する（テンプレートエンジンで埋め込む）
        const userEmail = "{{ auth()->user()->email }}";
        const confirmationPhrase = "delete my account";

        function validateForm() {
            const isEmailCorrect = emailInput.value === userEmail;
            const isPhraseCorrect = phraseInput.value.toLowerCase() === confirmationPhrase;
            
            deleteProfileButton.disabled = !(isEmailCorrect && isPhraseCorrect);
        }

        emailInput.addEventListener('input', validateForm);
        phraseInput.addEventListener('input', validateForm);
    });

    document.addEventListener('DOMContentLoaded', function () {
        const profileImageUrl = "{{ auth()->user()->profile_image_url }}";

        if(profileImageUrl){
            document.getElementById("removeProfileImageButton").disabled = false;
        } else{
            document.getElementById("removeProfileImageButton").disabled = true;
        }
    });

</script>

<style>
    .thumbnail {
        object-fit: cover;
        border-radius: 50%;
    }
</style>
