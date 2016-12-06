<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\Config;
use App;

class HideIdInViews extends Migration
{
    private $is_hr_ui = false;
    private $is_hr_role = false;
    
    private function checkUI_Role() {
        $list_id = Config::get('dx.employee_list_id', 0);
        
        $this->is_hr_ui = ($list_id > 0);   
        
        $this->is_hr_role  = (App::getLocale() == 'en');
    }
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->checkUI_Role();
        
        if (!$this->is_hr_ui) {
            return;
        }
                
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
        $this->checkUI_Role();
        
        if (!$this->is_hr_ui) {
            return;
        }
        
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
