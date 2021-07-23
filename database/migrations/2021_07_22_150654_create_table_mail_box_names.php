<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMailBoxNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_box_names', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mail_box_name');
            $table->unsignedInteger('community_user_id')->nullable();
            $table->unsignedInteger('global_ip_id')->nullable();
            $table->timestamp('arraival_at')->nullable();
            $table->timestamp('departure_at')->nullable();
            $table->boolean('current_stay')->default(true);
            $table->timestamp('posted_at')->nullable();
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
        Schema::dropIfExists('mail_box_names');
    }
}
