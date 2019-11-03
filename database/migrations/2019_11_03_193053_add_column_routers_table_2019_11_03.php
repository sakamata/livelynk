<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRoutersTable20191103 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->datetime('last_post_datetime')->nullable()->after('google_home_mac_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->dropColumn('last_post_datetime');
        });
    }
}
