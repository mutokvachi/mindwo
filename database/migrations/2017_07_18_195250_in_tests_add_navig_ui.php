<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class InTestsAddNavigUi extends EduMigration
{
    private $table_name = "edu_programms";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {
        DB::transaction(function () {
            
            $parent_menu = DB::table('dx_menu')->where('title', '=', trans('db_dx_menu.lbl_edu_learning'))->first();
            $list = \App\Libraries\DBHelper::getListByTable('in_tests');
            
            // create menu
            $arr_params = [
                'menu_list_id' => $list->id, 
                'list_title' => trans('db_dx_menu.lbl_edu_tests'),
                'menu_parent_id' => $parent_menu->id,
                'menu_order_index' => 45,
            ];
            App\Libraries\DBHelper::makeMenu($arr_params);
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
            DB::table('dx_menu')->where('title', '=', trans('db_dx_menu.lbl_edu_tests'))->delete();
        });
    }
}
