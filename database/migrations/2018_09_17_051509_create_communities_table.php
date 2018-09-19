<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('enable')->default(true);
            $table->integer('user_id')->unique()->nullable();
            $table->string('name', 32)->unique();
            $table->string('service_name', 48)->nullable();
            $table->string('url_path')->unique();
            $table->string('ifttt_event_name')->nullable();
            $table->string('ifttt_webhooks_key')->nullable();
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
        Schema::dropIfExists('communities');
    }
}
