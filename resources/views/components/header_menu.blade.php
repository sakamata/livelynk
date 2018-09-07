@auth
<div class="container">
    <div class="row">
        <div class="col-md-12" style="text-align:right;">
            <a href="{{ env("INDEX_PATH") }}" class="btn btn-info" role="button" style="margin-bottom:10px;">HOME</a>
            <a href="/admin_mac_address" class="btn btn-info" role="button" style="margin-bottom:10px;">MAC Address一覧</a>
            <a href="/admin_user" class="btn btn-info" role="button" style="margin-bottom:10px;">ユーザー一覧</a>
            <a href="/admin_user/add" class="btn btn-info" role="button" style="margin-bottom:10px;">新規ユーザー作成</a>
        </div>
    </div>
</div>
@endauth
