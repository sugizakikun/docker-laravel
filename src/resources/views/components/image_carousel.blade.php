
<div id="carouselExampleIndicators-{{$postId}}" class="carousel slide my-2" data-ride="carousel">
    @if(count($images) > 1)
        <ol class="carousel-indicators">
            @for($i=0 ; $i < count($images); $i++)
                <li data-target="#carouselExampleIndicators-{{$postId}}" data-slide-to="{{$i}}" class="active"></li>
            @endfor
        </ol>
    @endif
    <div class="carousel-inner">
        @foreach($images as $index => $image)
            @if($index ===0)
                <div class="carousel-item active">
                    <img class="d-block w-100 thumbnail" src="{{asset('img/moving.png')}}" height="200" width="300">
                </div>
            @else
                <div class="carousel-item">
                    <img class="d-block w-100 thumbnail" src="{{asset('img/moving.png')}}" height="200" width="300">
                </div>
            @endif
        @endforeach
    </div>
    <a class="carousel-control-prev" href="#carouselExampleIndicators-{{$postId}}" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators-{{$postId}}" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

