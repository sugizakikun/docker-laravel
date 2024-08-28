@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 g-4">
                <div class="col">
                    <div class="card bg-primary-subtle mb-3">
                        <img
                            src="{{ asset('img/subject/math.png')  }}"
                            onerror="this.onerror=null; this.src='{{ asset('img/subject/math.png')  }}';"
                            height="auto"
                            class="card-img-top"
                        >
                        <div class="card-body">
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-info-subtle mb-3">
                        <img
                            src="{{ asset('img/subject/social_studies.png')  }}"
                            onerror="this.onerror=null; this.src='{{ asset('img/subject/social_studies.png')  }}';"
                            height="auto"
                            class="card-img-top"
                        >
                        <div class="card-body">
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-success-subtle mb-3">
                        <img
                            src="{{ asset('img/subject/science.png')  }}"
                            onerror="this.onerror=null; this.src='{{ asset('img/subject/science.png')  }}';"
                            height="auto"
                            class="card-img-top"
                        >
                        <div class="card-body">
                            <p class="card-text">Some quick example text to build

                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-danger-subtle mb-3">
                        <img
                            src="{{ asset('img/subject/english.png')  }}"
                            onerror="this.onerror=null; this.src='{{ asset('img/subject/english.png')  }}';"
                            height="auto"
                            class="card-img-top"
                        >
                        <div class="card-body">
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        </div>
                    </div>
                </div>
            </div>

    </div>
</div>
@endsection
