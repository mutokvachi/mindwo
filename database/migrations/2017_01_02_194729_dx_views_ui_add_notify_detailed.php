<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxViewsUiAddNotifyDetailed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {            
            // pievieno lauku CMSā
            $list = App\Libraries\DBHelper::getListByTable("dx_views");            
                        
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'is_detailed_notify',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_views.is_detailed_notify_list'),
                'title_form' => trans('db_dx_views.is_detailed_notify_form'),
                'hint' => trans('db_dx_views.is_detailed_notify_hint'),
            ]);
        
            App\Libraries\DBHelper::addFieldToFormTab($list->id, $fld_id, trans('db_dx_views.tab_title'), 135);
            
            // remove old javascript
            \App\Libraries\DBHelper::removeJavaScriptFromForm('dx_views', trans('db_dx_views.js_showhide'));
            \App\Libraries\DBHelper::removeJavaScriptFromForm('dx_views', trans('db_dx_views.js_showhide_emails'));
            
            // add special JavaScript
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_views', '2017_01_02_controll.js', trans('db_dx_views.js_showhide'));
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_views', '2017_01_02_controll_emails.js', trans('db_dx_views.js_showhide_emails'));

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
            App\Libraries\DBHelper::removeFieldCMS("dx_views", "is_detailed_notify");
            
            App\Libraries\DBHelper::removeJavaScriptFromForm("dx_views", trans('db_dx_views.js_showhide'));
            App\Libraries\DBHelper::removeJavaScriptFromForm("dx_views", trans('db_dx_views.js_showhide_emails'));
            
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_views', '2016_12_27_controll.js', trans('db_dx_views.js_showhide'));
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_views', '2016_12_27_controll_emails.js', trans('db_dx_views.js_showhide_emails'));

        });
    }
}
