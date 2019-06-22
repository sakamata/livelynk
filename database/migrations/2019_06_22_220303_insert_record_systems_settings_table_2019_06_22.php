<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class InsertRecordSystemsSettingsTable20190622 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = Carbon::now();
        // 滞在者logを取る為の前回のチェック時間を設定
        DB::statement("
            INSERT INTO systems_settings(set_key, set_value)
                VALUES('last_log_check_datetime', '$now')
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            DELETE FROM systems_settings WHERE set_key='last_log_check_datetime'
        ");
    }
}
