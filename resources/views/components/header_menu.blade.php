@auth
<!-- 一般ログインユーザーメニュー -->
<a href="{{ env("INDEX_PATH") }}">HOME</a>
<a href="/admin_user/edit?id={{Auth::user()->id}}">プロフィール編集</a>
<a href="/admin_mac_address">デバイス一覧</a>
@can('normalAdmin')
<!-- 一般管理者メニュー。アイコン等でデザインに差をつけてもらうと嬉しいです。 -->
<a href="/admin_user">ユーザー一覧</a>
<a href="/admin_router">ルーター一覧</a>
<a href="/admin_community/edit?id={{Auth::user()->community_id}}">コミュニティ編集</a>
@endcan
@can('superAdmin')
<!-- 特別管理者メニュー。アイコン等でデザインに差をつけてもらうと嬉しいです。 -->
<a href="/admin_community">[特管]コミュニティ一覧</a>
@endcan
@endauth
