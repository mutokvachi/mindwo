<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorkflowLogicUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::create('dx_workflows_def', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('list_id')->nullable()->comment = "Reģistrs";
            $table->string('title')->nullable()->comment = "Nosaukums";
            $table->text('description')->nullable()->comment = "Apraksts";
            $table->date('valid_from')->nullable()->comment = "Spēkā no";
            $table->date('valid_to')->nullable()->comment = "Spēkā līdz";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('list_id');
            $table->foreign('list_id')->references('id')->on('dx_lists');
            
            $table->index('valid_from');
            $table->index('valid_to');
        });
        
        Schema::table('dx_workflows', function (Blueprint $table) {
            $table->integer('workflow_def_id')->nullable()->unsigned()->comment = "Darbplūsma";
            $table->integer('role_id')->nullable()->comment = "Loma";
            
            $table->index('workflow_def_id');
            $table->foreign('workflow_def_id')->references('id')->on('dx_workflows_def');
            
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
        Schema::table('dx_workflows', function (Blueprint $table) {
                       
            $table->dropForeign(['workflow_def_id']);
            $table->dropColumn(['workflow_def_id']);
            
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id']);
        });
        
        Schema::dropIfExists('dx_workflows_def');
    }
}
