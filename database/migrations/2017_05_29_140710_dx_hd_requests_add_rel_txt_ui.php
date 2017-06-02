<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxHdRequestsAddRelTxtUi extends Migration
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
            $obj = DB::table('dx_objects')->where('db_name', '=', 'dx_hd_requests')->first();
            $dub_list = DB::table('dx_lists')->where('object_id', '=', $obj->id)->where('list_title', '=', 'Biroja atbalsta pieteikumi')->first();
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $dub_list->id,
                'db_name' => 'job_place_kind',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_REL_TXT,
                'title_list' => 'Darba vieta',
                'title_form' => 'Darba vieta',
                'items' => 'Jauna;Esošā'
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($dub_list->id, $fld_id, ['order_index' => 42, 'row_type_id' => 1]); 
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $dub_list->id,
                'db_name' => 'mobile_kind',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_REL_TXT,
                'title_list' => 'Mobilais telefons',
                'title_form' => 'Mobilais telefons',
                'items' => 'Jauns;Esošais'
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($dub_list->id, $fld_id, ['order_index' => 44, 'row_type_id' => 1]); 
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $dub_list->id,
                'db_name' => 'mobilly_kind',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_REL_TXT,
                'title_list' => 'Mobilly',
                'title_form' => 'Mobilly',
                'items' => 'Pieslēgšana;Atslēgšana'
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($dub_list->id, $fld_id, ['order_index' => 46, 'row_type_id' => 1]);

            
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
                    ->where('list_title', '=', 'Biroja atbalsta pieteikumi')
                    ->first();                        
                        
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'job_place_kind');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'mobile_kind');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'mobilly_kind');
            
        });
    }
    
    private function isHelpDeskUI() {
        $klasif_menu = DB::table('dx_menu')->where('title', '=', 'Biroja atbalsts')->first();
          
        return ($klasif_menu) ? true : false;
    }
}
