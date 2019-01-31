<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditColumnsCommunitiesUsersStatusesTable20190131 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('communities_users_statuses', function (Blueprint $table) {
            $table->dateTime('last_access')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('communities_users_statuses', function (Blueprint $table) {
            $table->dropColumn('last_access');
        });
        Schema::table('communities_users_statuses', function (Blueprint $table) {
            $table->timestamp('last_access')->after('hide');
        });
    }
}
