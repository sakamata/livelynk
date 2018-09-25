@auth
<a href="{{ env("INDEX_PATH") }}">HOME</a>
<a href="/admin_user/edit?id={{Auth::user()->id}}">プロフィール編集</a>
<a href="/admin_mac_address">MAC Address一覧</a>
@can('normalAdmin')
<a href="/admin_user">ユーザー一覧</a>
<a href="/admin_community/edit?id={{Auth::user()->community_id}}">コミュニティ編集</a>
<a href="/admin_router">ルーター一覧</a>
@endcan
@can('superAdmin')
<a href="/admin_community">コミュニティ一覧</a>
@endcan
@endauth
