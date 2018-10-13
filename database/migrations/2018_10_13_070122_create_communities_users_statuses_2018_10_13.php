<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunitiesUsersStatuses20181013 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities_users_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id');
            $table->boolean('hide')->default(false);
            $table->timestamp('last_access');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('communities_users_statuses');
    }
}
