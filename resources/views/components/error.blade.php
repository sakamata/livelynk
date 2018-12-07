@if (count($errors) > 0)
<div class="alert alert-danger" role="alert">
    @foreach ($errors->all() as $error)
    <strong>エラー</strong>：&nbsp;{{ $error }}<br>
    @endforeach
    <button type="button" class="close" data-dismiss="alert" aria-label="閉じる">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
