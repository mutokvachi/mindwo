<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxMenuAddRoleId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_menu', function (Blueprint $table) {
            $table->integer('role_id')->nullable()->comment = 'Role';
            
            $table->index('role_id');            
            $table->foreign('role_id')->references('id')->on('dx_roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_menu', function (Blueprint $table) { 
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id']);
        });
    }
}
