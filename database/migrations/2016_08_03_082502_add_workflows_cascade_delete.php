<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkflowsCascadeDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('ALTER TABLE `dx_workflows_def` DROP FOREIGN KEY `dx_workflows_def_list_id_foreign`; ALTER TABLE `dx_workflows_def` ADD CONSTRAINT `dx_workflows_def_list_id_foreign` FOREIGN KEY (`list_id`) REFERENCES `dx_lists`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        DB::unprepared('ALTER TABLE `dx_workflows_info` DROP FOREIGN KEY `dx_workflows_info_workflow_def_id_foreign`; ALTER TABLE `dx_workflows_info` ADD CONSTRAINT `dx_workflows_info_workflow_def_id_foreign` FOREIGN KEY (`workflow_def_id`) REFERENCES `dx_workflows_def`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT');
        DB::unprepared('ALTER TABLE `dx_workflows` DROP FOREIGN KEY `dx_workflows_workflow_def_id_foreign`; ALTER TABLE `dx_workflows` ADD CONSTRAINT `dx_workflows_workflow_def_id_foreign` FOREIGN KEY (`workflow_def_id`) REFERENCES `dx_workflows_def`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        DB::unprepared('ALTER TABLE `dx_workflows_fields` DROP FOREIGN KEY `dx_workflows_fields_workflow_id_foreign`; ALTER TABLE `dx_workflows_fields` ADD CONSTRAINT `dx_workflows_fields_workflow_id_foreign` FOREIGN KEY (`workflow_id`) REFERENCES `dx_workflows`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
