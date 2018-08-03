@if (count($errors) > 0)
<div class="alert alert-danger" role="alert">
    @foreach ($errors->all() as $error)
    <strong>エラー</strong>：&nbsp;{{ $error }}<br>
    @endforeach
</div>
@endif
