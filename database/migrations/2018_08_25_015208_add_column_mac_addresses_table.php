<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMacAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mac_addresses', function (Blueprint $table) {
            $table->timestamp('posted_at')->nullable()->after('departure_at');
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
            $table->dropColumn('posted_at');
        });
    }
}
