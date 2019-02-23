<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditColumnCommunitiesTable20190223 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->boolean('calendar_enable')->default(false)->after('tumolink_enable');
            $table->string('calendar_public_iframe',1000)->nullable()->after('calendar_enable');
            $table->string('calendar_secret_iframe',1000)->nullable()->after('calendar_public_iframe');
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
            $table->dropColumn('calendar_enable');
            $table->dropColumn('calendar_public_iframe');
            $table->dropColumn('calendar_secret_iframe');
        });
    }
}
