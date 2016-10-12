<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeDeleteWfFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::unprepared('ALTER TABLE `dx_workflows_fields` DROP FOREIGN KEY `dx_workflows_fields_field_id_foreign`; ALTER TABLE `dx_workflows_fields` ADD CONSTRAINT `dx_workflows_fields_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `dx_lists_fields`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
            DB::unprepared('ALTER TABLE `dx_workflows_fields` DROP FOREIGN KEY `dx_workflows_fields_list_id_foreign`; ALTER TABLE `dx_workflows_fields` ADD CONSTRAINT `dx_workflows_fields_list_id_foreign` FOREIGN KEY (`list_id`) REFERENCES `dx_lists`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
            DB::unprepared('ALTER TABLE `dx_workflows` DROP FOREIGN KEY `dx_workflows_due_field_id_foreign`; ALTER TABLE `dx_workflows` ADD CONSTRAINT `dx_workflows_due_field_id_foreign` FOREIGN KEY (`due_field_id`) REFERENCES `dx_lists_fields`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
            DB::unprepared('ALTER TABLE `dx_workflows` DROP FOREIGN KEY `dx_workflows_resolution_field_id_foreign`; ALTER TABLE `dx_workflows` ADD CONSTRAINT `dx_workflows_resolution_field_id_foreign` FOREIGN KEY (`resolution_field_id`) REFERENCES `dx_lists_fields`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
            DB::unprepared('ALTER TABLE `dx_workflows` ADD CONSTRAINT `dx_workflows_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `dx_lists_fields`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        } catch (Exception $ex) {
            
        }
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
