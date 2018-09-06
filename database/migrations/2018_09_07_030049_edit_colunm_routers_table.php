<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditColunmRoutersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 恥ずかしい記録が残ってしまった
        Schema::table('routers', function (Blueprint $table) {
            $table->renameColumn('communitiy_id', 'community_id');
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
            $table->renameColumn('community_id', 'communitiy_id');
        });
    }
}
