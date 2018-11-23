@if(Auth::check())
@if(Auth::user()->community_id == 1)
<a href="https://geekoffice.herokuapp.com" target="_blank">ギークオフィスWebサービス</a>
@endif
@endif
<a href="https://tumolink.herokuapp.com/home" target="_blank">ツモリンク</a>
