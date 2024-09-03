<div>
    <img
        src="{{ $user->profile_image_url ? asset($user->profile_image_url): asset('img/profile_female.png') }}"
        onerror="this.onerror=null; this.src='{{ asset('img/profile_female.png')  }}';"
        class="thumbnail"
        width="{{$size}}"
        height="{{$size}}"
    >
</div>
