<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HideIdInViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $empl_list_id = Config::get('dx.employee_list_id');
        
        $form_id = DB::table('dx_forms')->where('list_id', '=', $empl_list_id)->first()->id;
        $tabs = DB::table('dx_forms_tabs')->where('form_id', '=', $form_id)->whereNotNull('grid_list_id')->get();
        
        foreach($tabs as $tab) {
            $view_id = DB::table('dx_views')->where('list_id', '=', $tab->grid_list_id)->first()->id;
            $id_fld_id = DB::table('dx_lists_fields')->where('list_id', '=', $tab->grid_list_id)->where('db_name', '=', 'id')->first()->id;
            
            DB::table('dx_views_fields')->where('view_id', '=', $view_id)->where('field_id', '=', $id_fld_id)->update(['is_hidden' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $empl_list_id = Config::get('dx.employee_list_id');
        
        $form_id = DB::table('dx_forms')->where('list_id', '=', $empl_list_id)->first()->id;
        $tabs = DB::table('dx_forms_tabs')->where('form_id', '=', $form_id)->whereNotNull('grid_list_id')->get();
        
        foreach($tabs as $tab) {
            $view_id = DB::table('dx_views')->where('list_id', '=', $tab->grid_list_id)->first()->id;
            $id_fld_id = DB::table('dx_lists_fields')->where('list_id', '=', $tab->grid_list_id)->where('db_name', '=', 'id')->first()->id;
            
            DB::table('dx_views_fields')->where('view_id', '=', $view_id)->where('field_id', '=', $id_fld_id)->update(['is_hidden' => 0]);
        }
    }
}
