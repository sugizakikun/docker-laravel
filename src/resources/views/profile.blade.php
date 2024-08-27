@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-dark border-bottom">Profile</h2>
            <div class="position-relative mb-3">
                <img src="{{ $user->profile_image_key ? asset($user->profile_image_key): asset('img/profile_female.png') }}" width="80%" height="auto">
                <div class="dropdown position-absolute color-bg-defaultcolor-fg-default px-2 py-1 left-0 bottom-0 ml-2 mb-2">
                    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Edit
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <button class="dropdown-item" data-toggle="modal" data-target="#updateProfileImage">Upload Image</button>
                        <button class="dropdown-item" data-toggle="modal" data-target="#removeProfileImage">Remove Image</button>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="name" value="{{$user->name}}">
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="email" value="{{$user->email}}">
                </div>
            </div>
            <div class="mb-3">
                <button class="btn btn-primary">Update Profile</button>
            </div>


            <h2 class="text-danger border-bottom">Delete Account</h2>
            <div class="mb-3">
                <p>Once you delete your account, there is no going back. Please be certain.</p>
                <button class="btn btn-danger" data-toggle="modal" data-target="#deleteAccount">Delete your account</button>
            </div>

            <!-- Modal(updateProfileImage) -->
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

            <!-- Modal(removeProfileImage) -->
            <div class="modal fade" id="removeProfileImage" tabindex="-1" role="dialog" aria-labelledby="removeProfileImage" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('profile.delete') }}" enctype="multipart/form-data">
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
                                <button type="submit" class="btn btn-danger" id="resetButton">リセットする</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal(deleteAccount) -->
            <div class="modal fade" id="deleteAccount" tabindex="-1" role="dialog" aria-labelledby="deleteAccount" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('withdraw') }}" enctype="multipart/form-data">
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
                                <button type="submit" class="btn btn-danger" id="deleteAccountButton" disabled>退会する</button>
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

    document.addEventListener('DOMContentLoaded', function () {
        const emailInput = document.getElementById('confirm-email');
        const phraseInput = document.getElementById('confirm-phrase');
        const deleteButton = document.getElementById('deleteAccountButton');

        // ユーザーのメールアドレスをサーバーから取得する（テンプレートエンジンで埋め込む）
        const userEmail = "{{ auth()->user()->email }}";
        const confirmationPhrase = "delete my account";

        function validateForm() {
            const isEmailCorrect = emailInput.value === userEmail;
            const isPhraseCorrect = phraseInput.value.toLowerCase() === confirmationPhrase;
            deleteButton.disabled = !(isEmailCorrect && isPhraseCorrect);
        }

        emailInput.addEventListener('input', validateForm);
        phraseInput.addEventListener('input', validateForm);
    });

</script>

<style>
    .alert{
        margin-bottom: 0px !important;
        border-radius: 0 !important;
    }
</style>
