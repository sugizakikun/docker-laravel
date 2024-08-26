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
                    <img src="{{ $user->profile_image_key ?? asset('img/profile_female.png') }}" witdh="75" height="75">
                    Welocome! {{$user->name}} san!

                    <h3>Email address</h3>
                    <p>{{$user->email}}</p>

                    <div class="mb-3 justify-content-center">
                        <a href="#">
                            <button class="btn btn-primary col-12">
                                <i class="fa fa-pen"></i>
                                プロフィールを編集する
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
