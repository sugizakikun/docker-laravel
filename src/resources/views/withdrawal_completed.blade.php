@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>退会完了！</h2>
            <p>またのご利用をお待ちしております。</p>

            <img src="{{asset('img/moving.png') }}" width="100%" height="auto">

            <a class="btn btn-primary" href="{{ route('login') }}">Top画面へ</a>
        </div>
    </div>
</div>
@endsection
