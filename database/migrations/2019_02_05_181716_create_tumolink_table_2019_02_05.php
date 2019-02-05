<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTumolinkTable20190205 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tumolink', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('community_user_id');
            $table->dateTime('maybe_arraival')->nullable()->default(null);
            $table->dateTime('maybe_departure')->nullable()->default(null);
            $table->boolean('google_home_push')->default(false);
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
        Schema::dropIfExists('tumolink');
    }
}
