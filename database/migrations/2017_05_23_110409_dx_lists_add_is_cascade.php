<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsAddIsCascade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_lists', function (Blueprint $table) {
            $table->boolean('is_cascade_delete')->nullable()->default(false)->comment = trans('db_dx_lists.is_cascade_delete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_lists', function (Blueprint $table) {
            $table->dropColumn(['is_cascade_delete']);
        });
    }
}
