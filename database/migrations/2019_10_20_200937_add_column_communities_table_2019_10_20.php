<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCommunitiesTable20191020 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->boolean('google_home_weather_enable')->default(false)->after('google_home_enable');
            $table->datetime('last_rainy_datetime')->nullable()->after('google_home_weather_enable')->comment('最終降雨日時');
            $table->double('latitude', 10, 7)->nullable()->after('last_rainy_datetime')->comment('緯度 ex 35.64');
            $table->double('longitude', 10, 7)->nullable()->after('latitude')->comment('経度 ex 139.71');
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
            $table->dropColumn('google_home_weather_enable');
            $table->dropColumn('last_rainy_datetime');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
}
