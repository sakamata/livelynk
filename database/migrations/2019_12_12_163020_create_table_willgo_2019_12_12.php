<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWillgo20191212 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('willgo', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('community_user_id');
            $table->dateTime('from_datetime')->nullable()->default(null);
            $table->dateTime('to_datetime')->nullable()->default(null);
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
        Schema::dropIfExists('willgo');
    }
}
