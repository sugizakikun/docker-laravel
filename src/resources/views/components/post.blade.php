<div>
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center">
            <x-profile_image :user="$post->author" size="40"></x-profile_image>
            <span>{{$post->author->name}}</span>
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
</div>
