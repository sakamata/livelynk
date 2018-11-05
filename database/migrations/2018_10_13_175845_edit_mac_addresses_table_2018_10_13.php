<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditMacAddressesTable20181013 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mac_addresses', function (Blueprint $table) {
            $table->renameColumn('user_id', 'community_user_id');
            $table->dropColumn('community_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mac_addresses', function (Blueprint $table) {
            $table->renameColumn('community_user_id', 'user_id');
            $table->integer('community_id')->nullable();
        });
    }
}
