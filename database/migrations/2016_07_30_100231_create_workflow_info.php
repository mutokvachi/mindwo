<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkflowInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('dx_workflows_info', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('init_user_id')->nullable()->comment = "Inicializēja";
            $table->integer('workflow_def_id')->nullable()->unsigned()->comment = "Darbplūsma";
            $table->datetime('init_time')->nullable()->comment = "Sākta";
            $table->datetime('end_time')->nullable()->comment = "Pabeigta";
            $table->integer('end_user_id')->nullable()->comment = "Pabeidza";
            $table->boolean('is_forced_end')->nullable()->comment = "Vai ir pabeigta forsēti";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('init_user_id');
            $table->foreign('init_user_id')->references('id')->on('dx_users');
            
            $table->index('end_user_id');
            $table->foreign('end_user_id')->references('id')->on('dx_users');
            
            $table->index('workflow_def_id');
            $table->foreign('workflow_def_id')->references('id')->on('dx_workflows_def');
        });
        
        Schema::table('dx_tasks', function (Blueprint $table) {
            $table->integer('wf_info_id')->nullable()->unsigned()->comment = "Darbplūsmas inicializācija";
            
            $table->index('wf_info_id');
            $table->foreign('wf_info_id')->references('id')->on('dx_workflows_info');
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
            $table->dropForeign(['wf_info_id']);
            $table->dropColumn(['wf_info_id']);
        });
        
        Schema::dropIfExists('dx_workflows_info');
    }
}
