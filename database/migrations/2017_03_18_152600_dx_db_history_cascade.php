<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxDbHistoryCascade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_db_history', function (Blueprint $table) {                       
            $table->dropForeign(['field_id']);
            $table->foreign('field_id')->references('id')->on('dx_lists_fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_db_history', function (Blueprint $table) {                       
            $table->dropForeign(['field_id']);
            $table->foreign('field_id')->references('id')->on('dx_lists_fields');
        });
    }
}
