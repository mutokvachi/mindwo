<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestTypesAddResponsible extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_hd_request_types', function (Blueprint $table) {
            $table->integer('resp_admin_id')->nullable()->comment = 'Administrators';
            $table->integer('resp_junior_id')->nullable()->comment = 'Jaunākais programmētājs';
            $table->integer('resp_programmer_id')->nullable()->comment = 'Programmētājs';
            
            $table->index('resp_admin_id');            
            $table->foreign('resp_admin_id')->references('id')->on('dx_users');
            
            $table->index('resp_junior_id');            
            $table->foreign('resp_junior_id')->references('id')->on('dx_users');
            
            $table->index('resp_programmer_id');            
            $table->foreign('resp_programmer_id')->references('id')->on('dx_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_hd_request_types', function (Blueprint $table) { 
            $table->dropForeign(['resp_admin_id']);
            $table->dropColumn(['resp_admin_id']);
            
            $table->dropForeign(['resp_junior_id']);
            $table->dropColumn(['resp_junior_id']);
            
            $table->dropForeign(['resp_programmer_id']);
            $table->dropColumn(['resp_programmer_id']);
        });
    }
}
