<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxDbEventsUiTuning extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {

            // reorganize view fields - hide or remove unneeded
            \App\Libraries\DBHelper::removeFieldsFromAllViews('dx_db_history', ['id'], true); // hide ID field
            \App\Libraries\DBHelper::removeFieldsFromAllForms('dx_db_events', ['id'], true); // hide ID field
            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable('dx_db_events')->id;   
            
            // adjust form look & feel
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'type_id')
                                                ->first()->id)
                    ->update(['row_type_id' => 3]);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'user_id')
                                                ->first()->id)
                    ->update(['row_type_id' => 3]);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'event_time')
                                                ->first()->id)
                    ->update(['row_type_id' => 3]);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'list_id')
                                                ->first()->id)
                    ->update(['row_type_id' => 2, 'order_index' => 10]);
            
             DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'item_id')
                                                ->first()->id)
                    ->update(['row_type_id' => 2, 'order_index' => 20]);
        });
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
