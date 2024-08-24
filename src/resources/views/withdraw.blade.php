@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">退会手続き</div>

                <div class="card-body">
                    <form method="post" action="{{ route('withdraw') }}" class="mb-3">
                        <input type="hidden" name="_method" value="DELETE"> <!-- この1行を追加！-->
                        @csrf
                        本当に退会しますか？

                        <button type="submit" class="btn btn-danger">退会する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
