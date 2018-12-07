<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCommunitiesTable20181206 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->text('service_name')->nullable(false)->change();
            $table->text('google_home_name')->nullable()->after('ifttt_webhooks_key');
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
        Schema::table('communities', function (Blueprint $table) {
            $table->text('service_name')->nullable()->change();
            $table->dropColumn('google_home_name');
            $table->dropColumn('google_home_mac_address');
        });
    }
}
