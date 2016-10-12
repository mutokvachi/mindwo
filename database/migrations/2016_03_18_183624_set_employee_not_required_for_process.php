<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetEmployeeNotRequiredForProcess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('in_processes', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
        });
        
        
        Schema::table('in_processes', function (Blueprint $table) {
            $table->integer('employee_id')->nullable()->unsigned()->change();            
        });
        
        Schema::table('in_processes', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('in_employees');          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_processes', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
        });
        
        Schema::table('in_processes', function (Blueprint $table) {            
            $table->integer('employee_id')->nullable(false)->unsigned()->change();
        });
        
        Schema::table('in_processes', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('in_employees');          
        });
    }
}
