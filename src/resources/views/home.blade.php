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
@endsection

<style>
    .thumbnail {
        object-fit: cover;
        border-radius: 50%;
    }
</style>

