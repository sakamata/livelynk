@if (session('message'))
<div class="alert" role="alert">
    {{ session('message') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="閉じる">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
