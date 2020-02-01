<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGobacks20200201 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gobacks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('community_user_id');
            $table->dateTime('maybe_departure')->nullable()->default(null);
            $table->boolean('google_home_push')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gobacks');
    }
}
