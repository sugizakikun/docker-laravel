@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @foreach($posts as $post)
                <x-post :post="$post"></x-post>
            @endforeach
        </div>
    </div>
</div>
@endsection

<style>
    .thumbnail {
        object-fit: cover;
        border-radius: 50%;
    }
</style>

