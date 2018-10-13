<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditColumnUsersTable20181013 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //email unique復活
            $table->unique('email');
            $table->dropColumn(['community_id', 'login_id', 'role', 'hide', 'last_access']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
            $table->integer('community_id')->after('id');
            $table->string('login_id')->unique()->nullable()->after('email');
            $table->string('role', 32)->default('normal')->after('facebook_id');
            $table->boolean('hide')->default(false)->after('remember_token');
            $table->timestamp('last_access')->after('hide');
        });
    }
}
