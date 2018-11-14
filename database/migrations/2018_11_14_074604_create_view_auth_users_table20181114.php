<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// 認証時に使用される view  Auth::user() に収納される
// $credentials array ('id', 'unique_name', 'password' )
class CreateViewAuthUsersTable20181114 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement( 'DROP VIEW IF EXISTS auth_users' );
        DB::statement( "
            CREATE VIEW auth_users AS
            SELECT
                community_user.id,
                community_user.user_id,
                community_user.community_id,
                users.name,
                users.unique_name,
                users.email,
                users.facebook_id,
                users.password,
                users.remember_token,
                communities.user_id AS reader_id,
                communities.name AS community_unique_name,
                communities.service_name,
                communities_users_statuses.role_id,
                communities_users_statuses.hide,
                communities_users_statuses.last_access,
                communities_users_statuses.created_at,
                communities_users_statuses.updated_at,
                roles.role
            FROM community_user
            JOIN users ON (community_user.user_id = users.id)
            JOIN communities ON (community_user.community_id = communities.id)
            JOIN communities_users_statuses ON (community_user.id = communities_users_statuses.id)
            JOIN roles ON (roles.id = communities_users_statuses.role_id)
        " );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement( 'DROP VIEW IF EXISTS auth_users' );
    }
}
