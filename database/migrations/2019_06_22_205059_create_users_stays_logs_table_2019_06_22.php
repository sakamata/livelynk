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
            $table->integer('community_user_id');
            $table->dateTime('arraival_at');
            $table->dateTime('departure_at')->nullable();
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
