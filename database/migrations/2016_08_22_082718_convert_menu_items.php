<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConvertMenuItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $menus = DB::table('dx_menu as m')
                 ->select('m.id', 'm.order_index', 'm.title', 'g.title as group_title')
                 ->leftJoin('dx_menu_groups as g', 'm.group_id', '=', 'g.id')
                 ->get();
        
        foreach($menus as $menu) {
            
            $path = $menu->group_title . ": [" . sprintf('%04d', $menu->order_index) . "] " . $menu->title;
            
            DB::table('dx_menu')
            ->where('id', '=', $menu->id)
            ->update(['title_index' => $path]);
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
