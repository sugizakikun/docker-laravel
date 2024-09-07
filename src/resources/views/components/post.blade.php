<div>
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center">
            <x-profile_image :user="$post->author" size="40"></x-profile_image>
            <span>{{$post->author->name}}</span>
            <div class="dropdown ml-auto">
                <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Edit
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <button class="dropdown-item" data-toggle="modal" data-target="#editPostModal-{{$post->id}}" id="editPostDropdownItemButton-{{$post->id}}">Edit Post</button>
                    <button class="dropdown-item" data-toggle="modal" data-target="#deletePostModal-{{$post->id}}" id="deletePostModalDropdownItemButton-{{$post->id}}">Delete Post</button>
                </div>
            </div>
        </div>

        <div class="card-body" style="white-space: pre-wrap;">
            {{ $post->content}}
        </div>

        @if(count($post->postImages) > 0)
            <x-image_carousel
                :images="$post->postImages"
                :postId="$post->id"
            ></x-image_carousel>
        @endif
    </div>

    <!-- Modal(updateProfileImage) -->
    <x-form_action_modal
        modalId="editPostModal-{{$post->id}}"
        submitButtonId="editPostModalButton-{{$post->id}}"
        route="{{ route('post.edit', ['postId' => $post->id]) }}"
        title="投稿の編集"
        buttonTitle="編集"
        method="PUT"
    >
        <!-- モーダルのボディ部分 -->
        <textarea
            class="form-control"
            rows="10"
            value="{{$post->content}}"
            id="content-{{$post->id}}"
            name="content"
        ></textarea>
    </x-form_action_modal>

    <!-- Modal(deletePost) -->
    <x-form_action_modal
        modalId="deletePostModal-{{$post->id}}"
        submitButtonId="deletePostModalButton-{{$post->id}}"
        route="{{ route('post.destroy', ['postId' => $post->id]) }}"
        title="投稿の削除"
        buttonTitle="削除"
        method="DELETE"
    >
        <!-- モーダルのボディ部分 -->
        <div class="modal-body">
            <p>この投稿を削除しますか？</p>
        </div>
    </x-form_action_modal>
</div>
