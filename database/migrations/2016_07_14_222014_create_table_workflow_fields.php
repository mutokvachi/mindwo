<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWorkflowFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_workflows_fields', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('workflow_id')->nullable()->comment = "Darbplūsma";
            $table->integer('list_id')->nullable()->comment = "Reģistrs";
            $table->integer('field_id')->nullable()->comment = "Lauks";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('workflow_id');
            $table->foreign('workflow_id')->references('id')->on('dx_workflows');
            
            $table->index('list_id');
            $table->foreign('list_id')->references('id')->on('dx_lists');
            
            $table->index('field_id');
            $table->foreign('field_id')->references('id')->on('dx_lists_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_workflows_fields');
    }
}
