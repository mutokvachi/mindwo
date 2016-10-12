<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CascadeDeleteEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
           DB::unprepared('ALTER TABLE `dx_db_events` DROP FOREIGN KEY `dx_db_events_list_id_foreign`; ALTER TABLE `dx_db_events` ADD CONSTRAINT `dx_db_events_list_id_foreign` FOREIGN KEY (`list_id`) REFERENCES `dx_lists`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
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
