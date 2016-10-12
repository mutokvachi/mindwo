<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomApproverFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_tasks', function (Blueprint $table) {
            $table->integer('wf_approv_id')->nullable()->unsigned()->comment = "Speciālais saskaņojums";
            
            $table->index('wf_approv_id');
            $table->foreign('wf_approv_id')->references('id')->on('dx_workflows_approve');
        });
        
        Schema::table('dx_workflows_approve', function (Blueprint $table) {
            $table->boolean('is_done')->nullable()->default(false)->comment = "Saskaņošana ir pabeigta";
            
            $table->index('is_done');
        });
        
        DB::table('dx_tasks_perform')->insert(['title' => 'Manuāli iestatīti saskaņotāji', 'code' => 'CUSTOM_APPROVERS', 'id' => 9]);
        
        DB::table('dx_tasks_types')->insert(['title' => 'Kritērijs - ir manuāli iestatīta saskaņošana', 'id' => 7]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_tasks', function (Blueprint $table) {
            $table->dropForeign(['wf_approv_id']);
            $table->dropColumn(['wf_approv_id']);
        });
        
        Schema::table('dx_workflows_approve', function (Blueprint $table) {
            $table->dropColumn(['is_done']);
        });
        
        DB::table('dx_tasks_perform')->where('id', '=', 9)->delete();
        
        DB::table('dx_tasks_types')->where('id', '=', 7)->delete();
    }
}
