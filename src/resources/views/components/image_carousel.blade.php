
<div id="carouselIndicators-{{$postId}}" class="carousel slide my-2" data-ride="carousel">
    @if(count($images) > 1)
        <ol class="carousel-indicators">
            @for($i=0 ; $i < count($images); $i++)
                <li data-target="#carouselIndicators-{{$postId}}" data-slide-to="{{$i}}" class="active"></li>
            @endfor
        </ol>
    @endif
    <div class="carousel-inner">
        @foreach($images as $index => $image)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img class="d-block w-100 thumbnail" src="{{asset('img/moving.png')}}" height="200" width="300">
            </div>
        @endforeach
    </div>
    <a class="carousel-control-prev" href="#carouselIndicators-{{$postId}}" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselIndicators-{{$postId}}" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

