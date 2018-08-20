@auth
<div class="container">
    <div class="row">
        <div class="col-md-12" style="text-align:right; padding-bottom:15px;">
            <a href="{{ env("INDEX_PATH") }}" class="btn btn-info" role="button">HOME</a>
            <a href="/admin_mac_address" class="btn btn-info" role="button">MAC Address一覧</a>
            <a href="/admin_user" class="btn btn-info" role="button">ユーザー一覧</a>
        </div>
    </div>
</div>
@endauth
