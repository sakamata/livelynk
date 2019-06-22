<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersStaysLogsTable20190622 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_stays_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('community_id')->unsigned();
            $table->foreign('community_id')->references('id')->on('communities'); //外部キー参照
            $table->integer('community_user_id')->unsigned();
            $table->foreign('community_user_id')->references('id')->on('community_user'); //外部キー参照
            $table->dateTime('arraival_at');
            $table->dateTime('departure_at')->nullable()->default(null);
            $table->dateTime('last_datetime');
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
        Schema::dropIfExists('users_stays_logs');
    }
}
