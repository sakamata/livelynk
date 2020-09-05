<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCommunitiesLastRainStopInfoDatetime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->datetime('last_rain_stop_info_datetime')
                ->default(date("Y-m-d H:i:s"))
                ->after('last_sunny_datetime')
                ->comment('雨止み通知最終時間');
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
            $table->dropColumn('last_rain_stop_info_datetime');
        });
    }
}
