<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class DxUsersVasUi extends EduMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {        
        DB::transaction(function () {
            
            // create parent menu
            $arr_params = [
                'menu_list_id' => null, 
                'list_title' => trans('db_dx_menu.lbl_edu_users'),
                'menu_parent_id' => null,
                'menu_order_index' => 20,
                'menu_icon' => 'fa fa-users',
            ];
            $parent_menu_id = App\Libraries\DBHelper::makeMenu($arr_params);
            
            $this->createUserList([
                'list_title' => trans('db_dx_users.list_title_edu'),
                'item_title' => trans('db_dx_users.item_title_edu'),
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 1,
                'criteria_role_title' => trans('db_dx_users.criteria_role_title_edu'),
                'criteria_role_field' => 'is_role_coordin_main'
            ], 1);
            
            $this->createUserList([
                'list_title' => trans('db_dx_users.list_title_org'),
                'item_title' => trans('db_dx_users.item_title_org'),
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 1,
                'criteria_role_title' => trans('db_dx_users.criteria_role_title_org'),
                'criteria_role_field' => 'is_role_coordin'
            ],1);
            
            $teachers_list_id = $this->createUserList([
                'list_title' => trans('db_dx_users.list_title_teacher'),
                'item_title' => trans('db_dx_users.item_title_teacher'),
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 0,
                'criteria_role_title' => trans('db_dx_users.criteria_role_title_teacher'),
                'criteria_role_field' => 'is_role_teacher'
            ]);
            
            \App\Libraries\DBHelper::removeFieldCMS($teachers_list_id, 'reg_addr_street');
            \App\Libraries\DBHelper::removeFieldCMS($teachers_list_id, 'reg_addr_city');
            \App\Libraries\DBHelper::removeFieldCMS($teachers_list_id, 'reg_addr_zip');
            
            $form = DB::table('dx_forms')->where('list_id', '=', $teachers_list_id)->first();
            
            $tab_pic_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_dx_users.tab_pictures'),
                'is_custom_data' => 1,
                'order_index' => 10
            ]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $teachers_list_id,
                'db_name' => 'picture_name',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_FILE,
                'title_list' => trans('db_dx_users.picture_name'),
                'title_form' => trans('db_dx_users.picture_name'),
                'is_public_file' => 1,
                'is_image_file' => 1
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($teachers_list_id, $fld_id, ['row_type_id' => 2, 'tab_id' => $tab_pic_id]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $teachers_list_id,
                'db_name' => 'sign_file_name',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_FILE,
                'title_list' => trans('db_dx_users.sign_file_name'),
                'title_form' => trans('db_dx_users.sign_file_name'),
                'hint' => trans('db_dx_users.sign_file_name_hint'),
                'is_public_file' => 1,
                'is_image_file' => 1
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($teachers_list_id, $fld_id, ['row_type_id' => 2, 'tab_id' => $tab_pic_id]);
                        
            
            $serv_list_id = $this->createUserList([
                'list_title' => trans('db_dx_users.list_title_serv'),
                'item_title' => trans('db_dx_users.item_title_serv'),
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 0,
                'criteria_role_title' => trans('db_dx_users.criteria_role_title_serv'),
                'criteria_role_field' => 'is_role_supply'
            ]);
            
            \App\Libraries\DBHelper::removeFieldCMS($serv_list_id, 'reg_addr_street');
            \App\Libraries\DBHelper::removeFieldCMS($serv_list_id, 'reg_addr_city');
            \App\Libraries\DBHelper::removeFieldCMS($serv_list_id, 'reg_addr_zip');
            
            $list_id = $this->createUserList([
                'list_title' => trans('db_dx_users.list_title_student'),
                'item_title' => trans('db_dx_users.item_title_student'),
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 1,
                'criteria_role_title' => trans('db_dx_users.criteria_role_title_student'),
                'criteria_role_field' => 'is_role_student'
            ]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'is_anonim',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_users.is_anonim'),
                'title_form' => trans('db_dx_users.is_anonim'),
                'hint' => trans('db_dx_users.is_anonim_hint'),              
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 5]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'login_name',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_dx_users.login_name_email'),
                'title_form' => trans('db_dx_users.login_name_email'),
                'max_lenght' => 200,
                'hint' => trans('db_dx_users.login_name_email_hint'),
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 8]);
            
            $all_list_id = $this->createUserList([
                'list_title' => trans('db_dx_users.list_title_all'),
                'item_title' => trans('db_dx_users.item_title_all'),
                'parent_menu_id' => -1,
                'person_code_required' => 0,
                'criteria_role_title' => null,
                'criteria_role_field' => null
            ]);
            
            \App\Libraries\DBHelper::removeFieldCMS($all_list_id, 'reg_addr_street');
            \App\Libraries\DBHelper::removeFieldCMS($all_list_id, 'reg_addr_city');
            \App\Libraries\DBHelper::removeFieldCMS($all_list_id, 'reg_addr_zip');
            
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
            $list_id = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_edu'))->first()->id;
            \App\Libraries\DBHelper::deleteRegister($list_id);
            
            $list_id = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_org'))->first()->id;
            \App\Libraries\DBHelper::deleteRegister($list_id);
            
            $list_id = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_teacher'))->first()->id;
            \App\Libraries\DBHelper::deleteRegister($list_id);
            
            $list_id = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_serv'))->first()->id;
            \App\Libraries\DBHelper::deleteRegister($list_id);
            
            $list_id = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_student'))->first()->id;
            \App\Libraries\DBHelper::deleteRegister($list_id);            
            
            $list_id = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_all'))->first()->id;
            \App\Libraries\DBHelper::deleteRegister($list_id);
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_dx_menu.lbl_edu_users'))
                    ->whereNull('parent_id')
                    ->delete();
        });
    }
        
    private function createUserList($arr_vals, $is_addr_tab = 0) {
        $list_id = App\Libraries\DBHelper::createUI([
            'table_name' => 'dx_users',
            'list_title' => $arr_vals['list_title'],
            'item_title' => $arr_vals['item_title'],
            'menu_parent_id' => $arr_vals['parent_menu_id']
        ]);
        
        $view = App\Libraries\DBHelper::getDefaultView($list_id);

        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'first_name',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => trans('db_dx_users.first_name'),
            'title_form' => trans('db_dx_users.first_name'),
            'max_lenght' => 50,
            'is_required' => 1
        ]);                                   
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => 3]);
        App\Libraries\DBHelper::addFieldToView($list_id, $view->id, $fld_id);
                
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'last_name',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => trans('db_dx_users.last_name'),
            'title_form' => trans('db_dx_users.last_name'),
            'max_lenght' => 50,
            'is_required' => 1
        ]);                                   
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => 3]); 
        App\Libraries\DBHelper::addFieldToView($list_id, $view->id, $fld_id);
        
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'person_code',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => trans('db_dx_users.person_code'),
            'title_form' => trans('db_dx_users.person_code'),
            'max_lenght' => 12,
            'is_required' => $arr_vals['person_code_required']
        ]);                                   
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => 3]);
        App\Libraries\DBHelper::addFieldToView($list_id, $view->id, $fld_id);
        
        if ($is_addr_tab) {
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            $tab_addr_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_dx_users.tab_addr'),
                'is_custom_data' => 1,
                'order_index' => 5
            ]);
        }
        
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'reg_addr_street',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => trans('db_dx_users.reg_addr_street'),
            'title_form' => trans('db_dx_users.reg_addr_street'),
            'max_lenght' => 200,
            'is_required' => 0
        ]);   
        $arr_prop = [];
        if ($is_addr_tab) {
            $arr_prop['tab_id'] = $tab_addr_id;
        }
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, $arr_prop);

        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'reg_addr_city',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => trans('db_dx_users.reg_addr_city'),
            'title_form' => trans('db_dx_users.reg_addr_city'),
            'max_lenght' => 100,
            'is_required' => 0
        ]);    
        $arr_prop = ['row_type_id' => 2];
        if ($is_addr_tab) {
            $arr_prop['tab_id'] = $tab_addr_id;
        }
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, $arr_prop ); 

        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'reg_addr_zip',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => trans('db_dx_users.reg_addr_zip'),
            'title_form' => trans('db_dx_users.reg_addr_zip'),
            'max_lenght' => 20,
            'is_required' => 0
        ]);  
        $arr_prop = ['row_type_id' => 2];
        if ($is_addr_tab) {
            $arr_prop['tab_id'] = $tab_addr_id;
        }
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, $arr_prop); 

        if ($arr_vals['criteria_role_title']) {
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => $arr_vals['criteria_role_field'],
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => $arr_vals['criteria_role_title'],
                'title_form' => $arr_vals['criteria_role_title'],
                'default_value' => 1,
                'criteria' => 1,
                'operation_id' => 1
            ]);  
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['is_hidden' => 1]);
        }
        
        $fld_display_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'display_name',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => 'Vārds, uzvārds',
            'title_form' => 'Vārds, uzvārds',
            'max_lenght' => 200
        ]); 
        
        // create view for using in related lookups
        $view_id = DB::table('dx_views')->insertGetId([
            'list_id' => $list_id,
            'title' => trans('db_dx_users.view_related'),
            'view_type_id' => 1,
            'is_hidden_from_main_grid' => 1,
            'is_hidden_from_tabs' => 1,
            'is_for_lookup' => 1,
        ]);
        
        DB::table('dx_views_fields')->insert([
           'list_id' => $list_id,
           'view_id' => $view_id,
           'field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'id')->first()->id,
        ]);
        
        DB::table('dx_views_fields')->insert([
           'list_id' => $list_id,
           'view_id' => $view_id,
           'field_id' => $fld_display_id,
        ]);
                
        return $list_id;
    }
}
