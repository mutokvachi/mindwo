<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeoffRequest extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_timeoff_requests', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('user_id')->comment = "Employee";
            $table->integer('timeoff_type_id')->unsigned()->comment = "Time off type";
            $table->date('from_date')->comment = "Date from";
            $table->date('to_date')->comment = "Date to";            
            $table->string('notes', 1000)->nullable()->comment = "Notes";
            $table->integer('dx_item_status_id')->default(1)->comment = "Status";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('timeoff_type_id');            
            $table->foreign('timeoff_type_id')->references('id')->on('dx_timeoff_types');
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users');
            
            $table->index('dx_item_status_id');            
            $table->foreign('dx_item_status_id')->references('id')->on('dx_item_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_timeoff_requests');
    }
}
