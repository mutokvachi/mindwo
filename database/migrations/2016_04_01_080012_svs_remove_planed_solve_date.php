<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SvsRemovePlanedSolveDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $form_row = DB::table('dx_forms')->where('list_id', '=', 227)->first();
        $view_row = DB::table('dx_views')->where('list_id', '=', 227)->first();
        
        $fld_row = DB::table('dx_lists_fields')->where('list_id', '=', 227)->where('db_name', '=', 'planned_resolve_time')->first();
        
        DB::table('dx_forms_fields')->where('list_id', '=', 227)->where('form_id', '=', $form_row->id)->where('field_id', '=', $fld_row->id)->delete();
        DB::table('dx_views_fields')->where('list_id', '=', 227)->where('view_id', '=', $view_row->id)->where('field_id', '=', $fld_row->id)->delete();
        
        DB::table('dx_db_history')->where('field_id', '=', $fld_row->id)->delete();
        DB::table('dx_lists_fields')->where('id', '=', $fld_row->id)->delete();
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
