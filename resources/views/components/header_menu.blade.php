@auth
<!-- 一般ログインユーザーメニュー -->
<a href="/">HOME</a>
<a href="/admin_user/edit?id={{Auth::user()->id}}">プロフィール編集</a>
@can('normalAdmin')
<!-- 一般管理者メニュー。アイコン等でデザインに差をつけてもらうと嬉しいです。 -->
<a href="/admin_user/add">新規ユーザー登録</a>
{{-- <a href="/admin_mac_address/regist">未登録デバイス一覧</a> --}}
<a href="/admin_user_provisional">仮ユーザー一覧</a>
<a href="/admin_user">ユーザー一覧</a>
<a href="/admin_mac_address/index">デバイス一覧</a>
<a href="/admin_router">ルーター一覧</a>
<a href="/admin_global_ip">グローバルIP一覧</a>
<a href="/admin_community/edit?id={{Auth::user()->community_id}}">コミュニティ編集</a>
<a href="/admin_log">滞在ログ一覧</a>
@endcan
@can('superAdmin')
<!-- 特別管理者メニュー。アイコン等でデザインに差をつけてもらうと嬉しいです。 -->
<a href="/admin_community">コミュニティ一覧</a>
@endcan
@endauth
