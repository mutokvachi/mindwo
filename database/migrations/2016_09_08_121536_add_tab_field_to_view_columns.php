<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTabFieldToViewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // pievienojam CMSā db jau eksistējosu lauku bet kas nebija pievienots UI pie skata
        $list = App\Libraries\DBHelper::getListByTable('dx_forms_fields');
        $view = App\Libraries\DBHelper::getDefaultView($list->id);
        
        $fld_id = DB::table('dx_lists_fields')->where('list_id', '=', $list->id)->where('db_name', '=', 'tab_id')->first()->id;
        App\Libraries\DBHelper::addFieldToView($list->id, $view->id, $fld_id);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_forms_fields');
        $view = App\Libraries\DBHelper::getDefaultView($list->id);
        
        $fld_id = DB::table('dx_lists_fields')->where('list_id', '=', $list->id)->where('db_name', '=', 'tab_id')->first()->id;
        
        DB::table('dx_views_fields')->where('view_id', '=', $view->id)->where('field_id', '=', $fld_id)->delete();
    }
}
