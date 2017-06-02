<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestsOfficeValidation extends Migration
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
            
            \App\Libraries\DBHelper::removeJavaScriptFromForm($dub_list->id, 'Biroja atbalsta pieteikuma validācija');
            \App\Libraries\DBHelper::addJavaScriptToForm($dub_list->id, '2017_06_02_dx_hd_requests.js', 'Biroja atbalsta pieteikuma validācija');

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
            $obj = DB::table('dx_objects')->where('db_name', '=', 'dx_hd_requests')->first();
            $dub_list = DB::table('dx_lists')->where('object_id', '=', $obj->id)->where('list_title', '=', 'Biroja atbalsta pieteikumi')->first();
            
            \App\Libraries\DBHelper::removeJavaScriptFromForm($dub_list->id, 'Biroja atbalsta pieteikuma validācija');
        });
    }
    
    private function isHelpDeskUI() {
        $klasif_menu = DB::table('dx_menu')->where('title', '=', 'Biroja atbalsts')->first();
          
        return ($klasif_menu) ? true : false;
    }
}
