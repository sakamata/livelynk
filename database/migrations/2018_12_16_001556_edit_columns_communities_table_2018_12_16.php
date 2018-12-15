<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditColumnsCommunitiesTable20181216 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->text('service_name_reading')->nullable()->after('service_name');
            $table->boolean('google_home_enable')->default(false)->after('ifttt_webhooks_key');
            $table->string('admin_comment',1000)->nullable()->after('google_home_mac_address');
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
            $table->dropColumn('service_name_reading');
            $table->dropColumn('google_home_enable');
            $table->dropColumn('admin_comment');
        });
    }
}
