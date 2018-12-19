<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditColumnsRoutersTable20181219 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->text('google_home_name')->nullable()->after('name');
            $table->text('google_home_mac_address')->nullable()->after('google_home_name');
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
            $table->dropColumn('google_home_name');
            $table->dropColumn('google_home_mac_address');
        });
    }
}
