<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxMenuAddIsTitleHiddenUi extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {      
        DB::transaction(function () {
            $list = App\Libraries\DBHelper::getListByTable("dx_menu");
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'is_title_hidden',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_menu.is_title_hidden'),
                'title_form' => trans('db_dx_menu.is_title_hidden'),
                'hint' => trans('db_dx_menu.hint_is_title_hidden'),
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['row_type_id' => 2]); 
            
            App\Libraries\DBHelper::updateFormField($list->id, 'title', ['row_type_id' => 2]);
            
            App\Libraries\DBHelper::reorderFormField($list->id, 'is_title_hidden', 'title');
            
            App\Libraries\DBHelper::updateFormField($list->id, 'id', ['row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list->id, 'group_id', ['row_type_id' => 2]);
            App\Libraries\DBHelper::reorderFormField($list->id, 'group_id', 'id');
            
            App\Libraries\DBHelper::updateFormField($list->id, 'fa_icon', ['row_type_id' => 3]);
            App\Libraries\DBHelper::updateFormField($list->id, 'color', ['row_type_id' => 3]);
            App\Libraries\DBHelper::updateFormField($list->id, 'order_index', ['row_type_id' => 3]);
            App\Libraries\DBHelper::reorderFormField($list->id, 'order_index', 'color');
            
            App\Libraries\DBHelper::updateFormField($list->id, 'list_id', ['row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list->id, 'url', ['row_type_id' => 2]);
            App\Libraries\DBHelper::reorderFormField($list->id, 'url', 'list_id');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        DB::transaction(function () {
            $list = App\Libraries\DBHelper::getListByTable("dx_menu");                       
                        
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'is_title_hidden');
            
        });
    }
    
    private function isHelpDeskUI() {
        $klasif_menu = DB::table('dx_menu')->where('title', '=', 'Biroja atbalsts')->first();
          
        return ($klasif_menu) ? true : false;
    }
}
