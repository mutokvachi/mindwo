<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToInEmployeesLeft extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_left_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->integer('created_user_id')->nullable()->unsigned();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable()->unsigned();
            $table->datetime('modified_time')->nullable();            
        });
        
        Schema::table('in_employees', function (Blueprint $table) {
            $table->string('office_address', 500)->nullable();
            $table->date('left_from')->nullable();
            $table->date('left_to')->nullable();
            $table->integer('left_reason_id')->nullable()->unsigned();
            $table->integer('substit_empl_id')->nullable()->unsigned();
            
            $table->index('left_from');
            $table->index('left_to');
            $table->index('left_reason_id');
            $table->index('substit_empl_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {        
                
        Schema::table('in_employees', function (Blueprint $table) {
            $table->dropColumn('office_address');
            $table->dropColumn('left_from');
            $table->dropColumn('left_to');
            $table->dropColumn('left_reason_id');
            $table->dropColumn('substit_empl_id');
        });
        
        Schema::dropIfExists('in_left_reasons');
    }
}
