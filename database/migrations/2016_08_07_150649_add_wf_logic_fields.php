<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWfLogicFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_workflows_def', function (Blueprint $table) {
            $table->boolean('is_custom_approve')->nullable()->comment = "Ir saskaņošanas iestatīšana";
        });
        
        Schema::table('dx_workflows_info', function (Blueprint $table) {
            $table->boolean('is_paralel_approve')->nullable()->comment = "Ir paralēlā saskaņošana";
        });
        
        Schema::create('dx_workflows_approve', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('workflow_info_id')->nullable()->unsigned()->comment = "Darbplūsmas instance";
            $table->integer('approver_id')->nullable()->comment = "Saskaņotājs";
            $table->integer('due_days')->nullable()->default(1)->comment = "Termiņš";
            $table->integer('order_index')->nullable()->default(100)->comment = "Secība";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('workflow_info_id');
            $table->foreign('workflow_info_id')->references('id')->on('dx_workflows_info');
            
            $table->index('approver_id');
            $table->foreign('approver_id')->references('id')->on('dx_users');
            
            $table->index('order_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_workflows_def', function (Blueprint $table) {
            $table->dropColumn(['is_custom_approve']);
        });
        
        Schema::table('dx_workflows_info', function (Blueprint $table) {
            $table->dropColumn(['is_paralel_approve']);
        });
        
        Schema::dropIfExists('dx_workflows_approve');
    }
}
