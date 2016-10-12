<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTasksFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_tasks', function (Blueprint $table) {
            $table->date('due_date')->nullable()->comment = "Termiņš";
            $table->integer('parent_task_id')->nullable()->comment = "Saistītais uzdevums";
            $table->integer('assigned_empl_id')->nullable()->comment = "Uzdevuma uzdevējs (deleģētājs)";
            
            $table->index('parent_task_id');
            $table->foreign('parent_task_id')->references('id')->on('dx_tasks');
            
            $table->index('assigned_empl_id');
            $table->foreign('assigned_empl_id')->references('id')->on('dx_users');
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
            $table->dropColumn(['due_date']);
            
            $table->dropForeign(['parent_task_id']);
            $table->dropColumn(['parent_task_id']);
            
            $table->dropForeign(['assigned_empl_id']);
            $table->dropColumn(['assigned_empl_id']);
        });
    }
}
