<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFormsTabsRequired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
           DB::unprepared("ALTER TABLE `dx_forms_tabs` CHANGE `grid_list_id` `grid_list_id` INT(11) NULL COMMENT 'Reference to register - rendered in the tab as grid';");
           DB::unprepared("ALTER TABLE `dx_forms_tabs` CHANGE `grid_list_field_id` `grid_list_field_id` INT(11) NULL COMMENT 'Reference to the register\'s field used to join';");
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
