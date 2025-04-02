<div>

    <div class="mb-3">
        @if (Session::get('info'))
        <div class="alert alert-info">
            {!! Session::get('info') !!}
        </div>
            <button class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        @endif

        @if (Session::get('fail'))
        <div class="alert alert-danger">
            {!! Session::get('fail') !!}
        </div>
            <button class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        @endif

        @if (Session::get('success'))
        <div class="alert alert-success">
            {!! Session::get('success') !!}
        </div>
            <button class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        @endif
    </div>

</div>