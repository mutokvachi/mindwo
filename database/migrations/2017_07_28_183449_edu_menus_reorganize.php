<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduMenusReorganize extends EduMigration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {       
        
        DB::transaction(function () {
            
            $parent_menu = DB::table('dx_menu')->where('title', '=', trans('db_dx_menu.lbl_edu_learning'))->first();
            $arr_params = [
                'menu_list_id' => null, 
                'list_title' => trans('db_dx_menu.lbl_edu_calendar'),
                'menu_parent_id' => $parent_menu->id,
                'menu_order_index' => 40,
                'menu_url' => 'calendar/scheduler/0'
            ];
            $new_id = App\Libraries\DBHelper::makeMenu($arr_params);
            
            DB::table('dx_menu')->where('id', '=', $new_id)->update(['head_title' => trans('db_dx_menu.lbl_edu_calendar_head')]);
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_edu_programms.list_name'))
                    ->update([
                        'head_title' => trans('db_edu_programms.head_title')
                    ]);
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_edu_modules_students.list_name'))
                    ->update([
                        'order_index' => 90
                    ]);
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_edu_materials.list_name'))
                    ->update([
                        'head_title' => trans('db_edu_materials.head_title')
                    ]);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function edu_down()
    {        
        DB::transaction(function () {
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_edu_programms.list_name'))
                    ->update([
                        'head_title' => null
                    ]);
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_edu_modules_students.list_name'))
                    ->update([
                        'order_index' => 40
                    ]);
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_edu_materials.list_name'))
                    ->update([
                        'head_title' => null
                    ]);
            
            DB::table('dx_menu')
                    ->where('head_title', '=', trans('db_dx_menu.lbl_edu_calendar_head'))
                    ->where('title', '=', trans('db_dx_menu.lbl_edu_calendar'))
                    ->delete();
        });
    }
}
