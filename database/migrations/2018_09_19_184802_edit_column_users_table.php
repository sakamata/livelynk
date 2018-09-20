<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditColumnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //email unique廃止
            $table->dropUnique('users_email_unique');
            $table->integer('community_id')->after('id');
            $table->string('facebook_id')->nullable()->after('email');
            $table->string('role', 32)->default('normal')->after('facebook_id');
            $table->dropColumn('admin_user');
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
            $table->unique('email');
            $table->dropColumn('community_id');
            $table->dropColumn('facebook_id');
            $table->dropColumn('role');
            // $table->boolean('admin_user')->default(false)->after('email');
        });
    }
}
