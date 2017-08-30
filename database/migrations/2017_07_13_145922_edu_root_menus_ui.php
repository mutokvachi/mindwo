<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduRootMenusUi extends EduMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {
        DB::transaction(function () {
            // create root menu Learning
            $arr_params = [
                'menu_list_id' => null, 
                'list_title' => trans('db_dx_menu.lbl_edu_learning'),
                'menu_parent_id' => null,
                'menu_order_index' => 30,
            ];
            App\Libraries\DBHelper::makeMenu($arr_params);
            
            // create root menu Users
            $arr_params = [
                'menu_list_id' => null, 
                'list_title' => trans('db_dx_menu.lbl_edu_users'),
                'menu_parent_id' => null,
                'menu_order_index' => 20,
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
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_dx_menu.lbl_edu_learning'))
                    ->whereNull('parent_id')
                    ->delete();
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_dx_menu.lbl_edu_users'))
                    ->whereNull('parent_id')
                    ->delete();
        });
    }
}
