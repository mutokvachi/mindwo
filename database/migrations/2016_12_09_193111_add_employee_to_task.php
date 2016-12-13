<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmployeeToTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_tasks', function (Blueprint $table) {
            $table->integer('item_empl_id')->nullable()->comment = "Employee";
            $table->index('item_empl_id');            
            $table->foreign('item_empl_id')->references('id')->on('dx_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_tasks', function (Blueprint $table) {   
            $table->dropForeign(['item_empl_id']);
            $table->dropColumn(['item_empl_id']);
        });
    }
}
