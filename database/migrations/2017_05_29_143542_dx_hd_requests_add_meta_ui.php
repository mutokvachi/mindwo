<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxHdRequestsAddMetaUi extends Migration
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
                'db_name' => 'subj_employee_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_LOOKUP,
                'title_list' => 'Darbinieks',
                'title_form' => 'Darbinieks',
                'rel_list_id' => Config::get('dx.employee_list_id'),
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($dub_list->id, $fld_id, ['order_index' => 47, 'row_type_id' => 1]); 
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $dub_list->id,
                'db_name' => 'phone_nr',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => 'Tālruņa nr.',
                'title_form' => 'Tālruņa nr.',
                'max_lenght' => 50
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($dub_list->id, $fld_id, ['order_index' => 49, 'row_type_id' => 1]);
            
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
                        
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'subj_employee_id');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'phone_nr');
            
        });
    }
    
    private function isHelpDeskUI() {
        $klasif_menu = DB::table('dx_menu')->where('title', '=', 'Biroja atbalsts')->first();
          
        return ($klasif_menu) ? true : false;
    }
}
