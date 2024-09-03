<!-- resources/views/components/modal.blade.php -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ $route }}" enctype="multipart/form-data">
                @csrf
                @isset($method)
                    <input type="hidden" name="_method" value="{{ $method }}">
                @endisset
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">{{ $title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{ $slot }}

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                    <button
                        type="submit"
                        class="btn btn-{{$method =="DELETE" ? 'danger' : 'primary'}}"
                        id="{{ $submitButtonId }}"
                    >
                        {{$buttonTitle}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
