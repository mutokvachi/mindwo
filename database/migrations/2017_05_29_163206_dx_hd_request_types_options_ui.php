<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxHdRequestTypesOptionsUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {            
        if (!$this->isHelpDeskUI()) {
            return;
        }
            
        DB::transaction(function () {
            $obj = DB::table('dx_objects')->where('db_name', '=', 'dx_hd_request_types')->first();
            $dub_list = DB::table('dx_lists')->where('object_id', '=', $obj->id)->where('list_title', '=', 'Pieteikumu veidi - biroja atbalsts')->first();
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $dub_list->id,
                'db_name' => 'is_work_place',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => 'Ir darba vietas lauks',
                'title_form' => 'Ir darba vietas lauks'
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($dub_list->id, $fld_id); 
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $dub_list->id,
                'db_name' => 'is_mobile',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => 'Ir mobilā tālruņa veida lauks',
                'title_form' => 'Ir mobilā tālruņa veida lauks'
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($dub_list->id, $fld_id); 
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $dub_list->id,
                'db_name' => 'is_mobilly',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => 'Ir mobilly veida lauks',
                'title_form' => 'Ir mobilly veida lauks'
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($dub_list->id, $fld_id); 
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $dub_list->id,
                'db_name' => 'is_empl',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => 'Ir darbinieka lauks',
                'title_form' => 'Ir darbinieka lauks'
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($dub_list->id, $fld_id);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $dub_list->id,
                'db_name' => 'is_mobnr',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => 'Ir tālruņa numura lauks',
                'title_form' => 'Ir tālruņa numura lauks'
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($dub_list->id, $fld_id);
            
            App\Libraries\DBHelper::removeFieldCMS($dub_list->id, 'resp_programmer_id');
            App\Libraries\DBHelper::removeFieldCMS($dub_list->id, 'resp_junior_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!$this->isHelpDeskUI()) {
            return;
        }
        
        DB::transaction(function () {
            
            $list = DB::table('dx_lists as l')
                    ->where('list_title', '=', 'Pieteikumu veidi - biroja atbalsts')
                    ->first();                        
                        
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'is_work_place');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'is_mobile');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'is_mobilly');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'is_empl');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'is_mobnr');
            
        });
    }
    
    private function isHelpDeskUI() {
        $klasif_menu = DB::table('dx_menu')->where('title', '=', 'Biroja atbalsts')->first();
          
        return ($klasif_menu) ? true : false;
    }
}
