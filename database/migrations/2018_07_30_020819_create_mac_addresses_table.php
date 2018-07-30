<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMacAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mac_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mac_address');
            $table->string('bender')->nullable();
            $table->string('device_name')->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('hide')->default(false);
            $table->integer('router_id')->nullable();
            $table->timestamp('arraival_at')->nullable();
            $table->timestamp('departure_at')->nullable();
            $table->boolean('current_stay')->default(false);
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
        Schema::dropIfExists('mac_addresses');
    }
}
