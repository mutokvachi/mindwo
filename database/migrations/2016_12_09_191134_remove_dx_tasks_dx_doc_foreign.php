<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDxTasksDxDocForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::unprepared('ALTER TABLE dx_tasks DROP FOREIGN KEY fk_dx_tasks_doc;');
        }
        catch(\Exception $e)
        {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('ALTER TABLE `dx_tasks` ADD CONSTRAINT `fk_dx_tasks_doc` FOREIGN KEY (`list_id`, `item_id`) REFERENCES `dx_doc`(`list_id`, `id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
    }
}