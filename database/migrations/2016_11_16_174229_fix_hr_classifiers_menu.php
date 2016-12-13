<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\Config;

class FixHrClassifiersMenu extends Migration
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
            
        
        // Documents        
        $menu_id = DB::table('dx_menu')->insertGetId([
            'parent_id' => 252, 
            'title' => 'Documents',
            'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10),
            'group_id'=>1,
            'position_id' => 1
        ]);
        
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Employee countries documents')->update(['parent_id' => $menu_id, 'order_index' => 10]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Employee document types')->update(['parent_id' => $menu_id, 'order_index' => 20]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Employee countries documents')->update(['parent_id' => $menu_id, 'order_index' => 30]);
    
        // Location        
        $menu_id = DB::table('dx_menu')->insertGetId([
            'parent_id' => 252, 
            'title' => 'Location',
            'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10),
            'group_id'=>1,
            'position_id' => 1
        ]);
        
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Countries')->update(['parent_id' => $menu_id, 'order_index' => 10]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Location types')->update(['parent_id' => $menu_id, 'order_index' => 20]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Timezones')->update(['parent_id' => $menu_id, 'order_index' => 30]);
        
        // Organization        
        $menu_id = DB::table('dx_menu')->insertGetId([
            'parent_id' => 252, 
            'title' => 'Organization',
            'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10),
            'group_id'=>1,
            'position_id' => 1
        ]);
        
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Legal entities')->update(['parent_id' => $menu_id, 'order_index' => 10]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Teams')->update(['parent_id' => $menu_id, 'order_index' => 20]);
        
        // Qualification        
        $menu_id = DB::table('dx_menu')->insertGetId([
            'parent_id' => 252, 
            'title' => 'Qualification',
            'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10),
            'group_id'=>1,
            'position_id' => 1
        ]);
        
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Education levels')->update(['parent_id' => $menu_id, 'order_index' => 10]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Languages')->update(['parent_id' => $menu_id, 'order_index' => 20]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Language levels')->update(['parent_id' => $menu_id, 'order_index' => 30]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Link types')->update(['parent_id' => $menu_id, 'order_index' => 40]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Qualif. doc. types')->update(['parent_id' => $menu_id, 'order_index' => 50]);

        // Work details      
        $menu_id = DB::table('dx_menu')->insertGetId([
            'parent_id' => 252, 
            'title' => 'Work details',
            'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10),
            'group_id'=>1,
            'position_id' => 1
        ]);
        
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Employments')->update(['parent_id' => $menu_id, 'order_index' => 10]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Job types')->update(['parent_id' => $menu_id, 'order_index' => 20]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Positions')->update(['parent_id' => $menu_id, 'order_index' => 30]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Termination reasons')->update(['parent_id' => $menu_id, 'order_index' => 40]);
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Termination types')->update(['parent_id' => $menu_id, 'order_index' => 40]);
        
        // Reindex all
        $menus = DB::table('dx_menu')->where('group_id', '=', '1')->get();
        
        foreach($menus as $menu) {
            DB::table('dx_menu')->where('id', '=', $menu->id)->update(['title_index' => 'SVS: [' . sprintf('%04d', $menu->order_index) . '] ' . $menu->title]);
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
