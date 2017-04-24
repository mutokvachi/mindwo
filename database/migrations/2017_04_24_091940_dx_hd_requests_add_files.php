<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestsAddFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_hd_requests', function (Blueprint $table) {
            $table->string('file2_name', 1000)->nullable()->comment = 'Datne 2';
            $table->string('file2_guid', 50)->nullable();
            
            $table->string('file3_name', 1000)->nullable()->comment = 'Datne 3';
            $table->string('file3_guid', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_hd_requests', function (Blueprint $table) {
            $table->dropColumn(['file2_name']);
            $table->dropColumn(['file2_guid']);
            $table->dropColumn(['file3_name']);
            $table->dropColumn(['file3_guid']);
        });
    }
}
