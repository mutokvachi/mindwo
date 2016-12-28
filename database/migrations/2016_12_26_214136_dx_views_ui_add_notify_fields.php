<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxViewsUiAddNotifyFields extends Migration
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
                'db_name' => 'is_email_sending',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_views.is_email_sending_list'),
                'title_form' => trans('db_dx_views.is_email_sending_form'),
                'hint' => trans('db_dx_views.is_email_sending_hint'),
            ]);
        
            App\Libraries\DBHelper::addFieldToFormTab($list->id, $fld_id, trans('db_dx_views.tab_title'), 0);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'email_receivers',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_dx_views.email_receivers_list'),
                'title_form' => trans('db_dx_views.email_receivers_form'),
                'hint' => trans('db_dx_views.email_receivers_hint'),
                'max_lenght' => 1000
            ]);
        
            App\Libraries\DBHelper::addFieldToFormTab($list->id, $fld_id, trans('db_dx_views.tab_title'), 0);
            
            $rel_list = App\Libraries\DBHelper::getListByTable('dx_roles');
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'role_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'title_list' => trans('db_dx_views.role_id_list'),
                'title_form' => trans('db_dx_views.role_id_form'),
                'hint' => trans('db_dx_views.role_id_hint'),
                'rel_list_id' => $rel_list->id,
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $rel_list->id)->where('db_name','=', 'title')->first()->id,
            ]);
        
            App\Libraries\DBHelper::addFieldToFormTab($list->id, $fld_id, trans('db_dx_views.tab_title'), 0);
            
            $rel_list = App\Libraries\DBHelper::getListByTable('dx_lists_fields');
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'field_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'title_list' => trans('db_dx_views.field_id_list'),
                'title_form' => trans('db_dx_views.field_id_form'),
                'hint' => trans('db_dx_views.field_id_hint'),
                'rel_list_id' => $rel_list->id,
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $rel_list->id)->where('db_name','=', 'title_list')->first()->id,
            ]);
        
            App\Libraries\DBHelper::addFieldToFormTab($list->id, $fld_id, trans('db_dx_views.tab_title'), 0);
                       
            $fld = DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list->id)
                    ->where('db_name', '=', 'is_for_monitoring')
                    ->first();
            
            if ($fld) {
                // update existing field is_for_monitoring
                DB::table('dx_lists_fields')
                        ->where('id', '=', $fld->id)
                        ->update([
                            'hint' => trans('db_dx_views.is_for_monitoring_hint'),
                            'title_list' => trans('db_dx_views.is_for_monitoring_list'),
                            'title_form' => trans('db_dx_views.is_for_monitoring_form'),
                        ]);
                $form = DB::table('dx_forms')->where('list_id', '=', $list->id)->first();
                $tab = DB::table('dx_forms_tabs')->where('form_id', '=', $form->id)->where('title', '=', trans('db_dx_views.tab_title'))->first();

                DB::table('dx_forms_fields')->where('form_id', '=', $form->id)->where('field_id', '=', $fld->id)->update([                
                    'tab_id' => $tab->id,
                    'order_index' => 1
                ]);
            }
            else {
                $fld_id = DB::table('dx_lists_fields')->insertGetId([
                    'list_id' => $list->id,
                    'db_name' => 'is_for_monitoring',
                    'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                    'title_list' => trans('db_dx_views.is_for_monitoring_list'),
                    'title_form' => trans('db_dx_views.is_for_monitoring_form'),
                    'hint' => trans('db_dx_views.is_for_monitoring_hint'),
                ]);

                App\Libraries\DBHelper::addFieldToFormTab($list->id, $fld_id, trans('db_dx_views.tab_title'), 1);
            }
            
            // add special JavaScript
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_views', '2016_12_27_controll.js', trans('db_dx_views.js_showhide'));
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_views', '2016_12_27_controll_emails.js', trans('db_dx_views.js_showhide_emails'));

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
            App\Libraries\DBHelper::removeFieldCMS("dx_views", "is_for_monitoring");
            App\Libraries\DBHelper::removeFieldCMS("dx_views", "is_email_sending");
            App\Libraries\DBHelper::removeFieldCMS("dx_views", "email_receivers");
            App\Libraries\DBHelper::removeFieldCMS("dx_views", "role_id");
            App\Libraries\DBHelper::removeFieldCMS("dx_views", "field_id");
            
            $list = App\Libraries\DBHelper::getListByTable("dx_views");
            $form = DB::table('dx_forms')->where('list_id', '=', $list->id)->first();
            DB::table('dx_forms_tabs')->where('form_id', '=', $form->id)->where('title', '=', trans('db_dx_views.tab_title'))->delete();
            
            App\Libraries\DBHelper::removeJavaScriptFromForm("dx_views", trans('db_dx_views.js_showhide'));
            App\Libraries\DBHelper::removeJavaScriptFromForm("dx_views", trans('db_dx_views.js_showhide_emails'));
        });
    }
}
