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
                            <button class="dropdown-item" data-toggle="modal" data-target="#updateProfileImageModal" id="updateProfileImageDropdownItemButton">Upload Image</button>
                            <button class="dropdown-item" data-toggle="modal" data-target="#removeProfileImageModal" id="removeProfileImageDropdownItemButton">Remove Image</button>
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
            <x-form_action_modal
                modalId="updateProfileImageModal"
                submitButtonId="uploadProfileImageModalButton"
                route="{{ route('profile_image.edit') }}"
                title="プロフィール画像の変更"
                buttonTitle="アップロード"
                method="PUT"
            >
                <!-- モーダルのボディ部分 -->
                <div class="modal-body">
                    <input class="form-control" type="file" name="image" id="imageInput" aria-describedby="fileHelp" aria-required="true">
                    <div id="fileHelp" class="form-text">許可されるファイル形式: JPG, PNG, GIF, WebP, AVIF (最大1MB)</div>
                </div>
            </x-form_action_modal>

            <!-- Modal(removeProfileImage) -->
            <x-form_action_modal
                modalId="removeProfileImageModal"
                submitButtonId="removeProfileImageModalButton"
                route="{{ route('profile_image.delete') }}"
                title="プロフィール画像の削除"
                buttonTitle="リセット"
                method="DELETE"
            >
                <!-- モーダルのボディ部分 -->
                <div class="modal-body">
                    <p>プロフィール画像をリセットしてもよろしいですか？</p>
                </div>
            </x-form_action_modal>

            <!-- Modal(deleteProfile) -->
            <x-form_action_modal
                modalId="deleteProfileModal"
                submitButtonId="deleteProfileModalButton"
                route="{{ route('profile.destroy') }}"
                title="アカウントの削除"
                buttonTitle="退会する"
                method="DELETE"
            >
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <div>
                        This is extremely important.
                    </div>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_method" value="DELETE">
                    <p>本当に退会しますか？</p>
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
            </x-form_action_modal>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const uploadProfileImageModalButton = document.getElementById('uploadProfileImageModalButton');
        uploadProfileImageModalButton.disabled = true

        document.getElementById('imageInput').addEventListener('change', function() {

            if (this.files && this.files.length > 0) {
                const file = this.files[0];
                const maxSize = 1024 * 1024; // 1MB
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif'];

                if (!allowedTypes.includes(file.type)) {
                    alert('許可されていないファイル形式です。JPG、PNG、GIF、WebP、AVIF形式のファイルを選択してください。');
                    this.value = ''; // Clear the input
                    uploadProfileImageModalButton.disabled = true; // Keep the button disabled
                    return;
                }

                if (file.size > maxSize) {
                    alert('ファイルサイズが大きすぎます。1MB以下のファイルを選択してください。');
                    this.value = ''; // Clear the input
                    uploadProfileImageModalButton.disabled = true; // Keep the button disabled
                    return;
                }

                uploadProfileImageModalButton.disabled = false; // Enable the button
            } else {
                uploadProfileImageModalButton.disabled = true; // Keep the button disabled
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const profileImageUrl = "{{ auth()->user()->profile_image_url }}";

        const removeProfileImageDropdownItemButton = document.getElementById("removeProfileImageDropdownItemButton");
        const removeProfileImageModalButton = document.getElementById("removeProfileImageModalButton");

        if (profileImageUrl) {
            removeProfileImageDropdownItemButton.disabled = false;
            removeProfileImageModalButton.disabled = false;
        } else {
            removeProfileImageDropdownItemButton.disabled = true;
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const emailInput = document.getElementById('confirm-email');
        const phraseInput = document.getElementById('confirm-phrase');

        const deleteProfileModalButton = document.getElementById('deleteProfileModalButton');
        deleteProfileModalButton.disabled = true;

        // ユーザーのメールアドレスをサーバーから取得する（テンプレートエンジンで埋め込む）
        const userEmail = "{{ auth()->user()->email }}";
        const confirmationPhrase = "delete my account";

        function validateForm() {
            const isEmailCorrect = emailInput.value === userEmail;
            const isPhraseCorrect = phraseInput.value.toLowerCase() === confirmationPhrase;

            deleteProfileModalButton.disabled = !(isEmailCorrect && isPhraseCorrect);
        }

        emailInput.addEventListener('input', validateForm);
        phraseInput.addEventListener('input', validateForm);
    });

</script>

<style>
    .thumbnail {
        object-fit: cover;
        border-radius: 50%;
    }
</style>
