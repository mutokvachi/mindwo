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
            
            $parent_menu_id = DB::table('dx_menu')->where('title', '=', trans('db_dx_menu.lbl_edu_users'))->where('id', '!=', 13)->first()->id;
            
            $main_coord_list_id = $this->createUserList([
                'list_title' => trans('db_dx_users.list_title_edu'),
                'item_title' => trans('db_dx_users.item_title_edu'),
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 1,
                'criteria_role_title' => trans('db_dx_users.criteria_role_title_edu'),
                'criteria_role_field' => 'is_role_coordin_main'
            ]);
            
            $this->createUserList([
                'list_title' => trans('db_dx_users.list_title_org'),
                'item_title' => trans('db_dx_users.item_title_org'),
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 1,
                'criteria_role_title' => trans('db_dx_users.criteria_role_title_org'),
                'criteria_role_field' => 'is_role_coordin'
            ]);
            
            $teachers_list_id = $this->createUserList([
                'list_title' => trans('db_dx_users.list_title_teacher'),
                'item_title' => trans('db_dx_users.item_title_teacher'),
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 0,
                'criteria_role_title' => trans('db_dx_users.criteria_role_title_teacher'),
                'criteria_role_field' => 'is_role_teacher'
            ]);
           
            
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
                
            // Create students register
            $list_id = $this->createUserList([
                'list_title' => trans('db_dx_users.list_title_student'),
                'item_title' => trans('db_dx_users.item_title_student'),
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 1,
                'criteria_role_title' => trans('db_dx_users.criteria_role_title_student'),
                'criteria_role_field' => 'is_role_student'
            ], 4);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'is_anonim',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_users.is_anonim'),
                'title_form' => trans('db_dx_users.is_anonim'),
                'hint' => trans('db_dx_users.is_anonim_hint'),              
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 5, 'row_type_id' => 4]);
            
            $region_list = \App\Libraries\DBHelper::getListByTable('dx_regions');
            $region_display = DB::table('dx_lists_fields')->where('list_id', '=', $region_list->id)->where('db_name', '=', 'title')->first();
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'region_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_LOOKUP,
                'title_list' => trans('db_dx_users.region_id'),
                'title_form' => trans('db_dx_users.region_id'),
                'hint' => trans('db_dx_users.region_id_hint'),
                'rel_list_id' => $region_list->id,
                'rel_display_field_id' => $region_display->id
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 31]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'login_name',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_EMAIL,
                'title_list' => trans('db_dx_users.login_name_email'),
                'title_form' => trans('db_dx_users.login_name_email'),
                'max_lenght' => 200,
                'hint' => trans('db_dx_users.login_name_email_hint'),
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 32]);
            
            $def_view_id = DB::table('dx_views')
                    ->where('list_id', '=', $list_id)
                    ->where('title', '=', trans('db_dx_users.list_title_student'))
                    ->first()
                    ->id;
            
            DB::table('dx_views')
                    ->where('id', '=', $def_view_id)
                    ->update([
                        'view_type_id' => 9,
                        'custom_sql' => "
                            SELECT * FROM (
                                select
                                        u.id,
                                        u.person_code,
                                        u.first_name,
                                        u.last_name,
                                        u.full_name_code,
                                        u.login_name,
                                        u.is_anonim,
                                        u.is_role_student,
                                        u.is_role_coordin_main,
                                        o.title as org_title,
                                        ou.job_title as job_title,
                                        ou.email as email,
                                        ou.phone as phone,
                                        ou.mobile as mobile
                                from
                                        dx_users u
                                        left join edu_orgs_users ou on u.id = ou.user_id
                                        left join edu_orgs o on ou.org_id = o.id
                                where
                                        u.is_role_student = 1 and
                                        (
                                        exists(select id from dx_users_roles ru where ru.role_id in (1, 74) and ru.user_id=[ME])
                                        or
                                        ou.org_id in (select org_id from edu_orgs_users where user_id = [ME] and ifnull(end_date, DATE_ADD(now(), INTERVAL 1 DAY)) >=now())
                                        )
                                ) tb 
                            WHERE 1 = 1
                        " 
            ]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'org_title',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_edu_orgs_users.org_id'),
                'title_form' => trans('db_edu_orgs_users.org_id'),
                'formula' => '[' . trans('db_edu_orgs_users.org_id') .']',
            ]);                                   
            App\Libraries\DBHelper::addFieldToView ($list_id, $def_view_id, $fld_id); 
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'job_title',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_edu_orgs_users.job_title'),
                'title_form' => trans('db_edu_orgs_users.job_title'),
                'formula' => '[' . trans('db_edu_orgs_users.job_title') .']',
            ]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'email',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_EMAIL,
                'title_list' => trans('db_edu_orgs_users.email'),
                'title_form' => trans('db_edu_orgs_users.email'),
                'formula' => '[' . trans('db_edu_orgs_users.email') .']',
            ]);                                   
            App\Libraries\DBHelper::addFieldToView ($list_id, $def_view_id, $fld_id);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'phone',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_MOBILE,
                'title_list' => trans('db_edu_orgs_users.phone'),
                'title_form' => trans('db_edu_orgs_users.phone'),
                'formula' => '[' . trans('db_edu_orgs_users.phone') .']',
            ]);                                   
            App\Libraries\DBHelper::addFieldToView ($list_id, $def_view_id, $fld_id);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'mobile',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_MOBILE,
                'title_list' => trans('db_edu_orgs_users.mobile'),
                'title_form' => trans('db_edu_orgs_users.mobile'),
                'formula' => '[' . trans('db_edu_orgs_users.mobile') .']',
            ]);                                   
            App\Libraries\DBHelper::addFieldToView ($list_id, $def_view_id, $fld_id);
            
            // Create users lookup register for all types of users
            $all_list_id = $this->createUserList([
                'list_title' => trans('db_dx_users.list_title_all'),
                'item_title' => trans('db_dx_users.item_title_all'),
                'parent_menu_id' => -1,
                'person_code_required' => 0,
                'criteria_role_title' => null,
                'criteria_role_field' => null
            ]);
                        
            $def_view_id = DB::table('dx_views')
                    ->where('list_id', '=', $all_list_id)
                    ->where('title', '=', trans('db_dx_users.list_title_all'))
                    ->first()
                    ->id;
            
            DB::table('dx_views')
                    ->where('id', '=', $def_view_id)
                    ->update([
                        'view_type_id' => 9,
                        'custom_sql' => "
                            SELECT * FROM (
                                select distinct
                                        dx_users.id as id,
                                        dx_users.full_name_code as dx_users_full_name_code,
                                        dx_users.full_name_code
                                from
                                        dx_users
                                        left join edu_orgs_users ou on dx_users.id = ou.user_id
                                        left join edu_orgs o on ou.org_id = o.id
                                where
                                        (dx_users.is_role_student = 1 or
                                         dx_users.is_role_teacher = 1 or
                                         dx_users.is_role_supply = 1 or
                                         dx_users.is_role_coordin_main = 1 or
                                         dx_users.is_role_coordin = 1) and
                                        (
                                        exists(select id from dx_users_roles ru where ru.role_id in (1, 74) and ru.user_id=[ME])
                                        or
                                        ou.org_id in (select org_id from edu_orgs_users where user_id = [ME] and ifnull(end_date, DATE_ADD(now(), INTERVAL 1 DAY)) >=now())
                                        )
                                ) tb 
                            WHERE 1 = 1
                        " 
            ]);
            
            // Create user profile register
            $profile_list_id = $this->createUserList([
                'list_title' => trans('db_dx_users.list_title_profile'),
                'item_title' => trans('db_dx_users.item_title_profile'),
                'parent_menu_id' => -1,
                'person_code_required' => 1,
                'criteria_role_title' => null,
                'criteria_role_field' => null
            ], 3, true);
            
            $form = DB::table('dx_forms')->where('list_id', '=', $profile_list_id)->first();
            
            $tab_general_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_dx_users.tab_general'),
                'is_custom_data' => 1,
                'order_index' => 2
            ]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $profile_list_id,
                'db_name' => 'region_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_LOOKUP,
                'title_list' => trans('db_dx_users.region_id'),
                'title_form' => trans('db_dx_users.region_id'),
                'hint' => trans('db_dx_users.region_id_hint'),
                'rel_list_id' => $region_list->id,
                'rel_display_field_id' => $region_display->id,
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($profile_list_id, $fld_id, ['tab_id' => $tab_general_id]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $profile_list_id,
                'db_name' => 'login_name',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_EMAIL,
                'title_list' => trans('db_dx_users.login_name_email'),
                'title_form' => trans('db_dx_users.login_name_email'),
                'max_lenght' => 200,
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($profile_list_id, $fld_id, ['row_type_id' => 2, 'tab_id' => $tab_general_id]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $profile_list_id,
                'db_name' => 'mobile',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_MOBILE,
                'title_list' => trans('db_dx_users.mobile'),
                'title_form' => trans('db_dx_users.mobile'),
                'max_lenght' => 50,
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($profile_list_id, $fld_id, ['row_type_id' => 2, 'tab_id' => $tab_general_id]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $profile_list_id,
                'db_name' => 'is_receive_notify',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_users.is_receive_notify'),
                'title_form' => trans('db_dx_users.is_receive_notify'),
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($profile_list_id, $fld_id, ['tab_id' => $tab_general_id]);
            
            $tab_bank_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_dx_users.tab_bank'),
                'is_custom_data' => 1,
                'order_index' => 6
            ]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $profile_list_id,
                'db_name' => 'iban_nr',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_dx_users.iban_nr'),
                'title_form' => trans('db_dx_users.iban_nr'),
                'max_lenght' => 50,
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($profile_list_id, $fld_id, ['tab_id' => $tab_bank_id]);

            // create my profile menu
            $arr_params = [
                'menu_list_id' => null, 
                'list_title' => trans('db_dx_menu.lbl_edu_myprofile'),
                'menu_parent_id' => null,
                'menu_order_index' => 15,
                'menu_url' => 'edu_profile',
            ];
            $profile_menu_id = App\Libraries\DBHelper::makeMenu($arr_params);
            
            DB::table('dx_config')->where('config_name', '=', 'SCRIPT_JS')->update([
                'val_script' => "
                    $(document).ready(function() {  
                        var my_list_id = " . $profile_list_id . ";
                        var menu = $('#navbar').find('a[href$=edu_profile]');
                                                
                        if (menu.length) {
                            var li = menu.parent();
                            var htm = menu.html();
                            menu.remove();
                            var a = $('<a>');
                            a.attr('href', 'javascript:;');
                            a.html(htm);
                            a.addClass('edu_profile');
                            a.click(function() {
                                open_form('form', $('body').data('user-id'), my_list_id, 0, 0, '', 0, '');       
                            });	
                            
                            li.append(a);
                        }
                    });
                "
            ]);
            
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
            
            $list_id = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_profile'))->first()->id;
            \App\Libraries\DBHelper::deleteRegister($list_id);
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_dx_menu.lbl_edu_myprofile'))
                    ->whereNull('parent_id')
                    ->delete();
        });
    }
        
    private function createUserList($arr_vals, $row_type_id = 3, $is_read_only = false) {
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
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => $row_type_id, 'is_readonly' => $is_read_only]);
        App\Libraries\DBHelper::addFieldToView($list_id, $view->id, $fld_id, ['is_item_link' => 1]);
                
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'last_name',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => trans('db_dx_users.last_name'),
            'title_form' => trans('db_dx_users.last_name'),
            'max_lenght' => 50,
            'is_required' => 1
        ]);                                   
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => $row_type_id, 'is_readonly' => $is_read_only]); 
        App\Libraries\DBHelper::addFieldToView($list_id, $view->id, $fld_id, ['is_item_link' => 1]);
        
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'person_code',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => trans('db_dx_users.person_code'),
            'title_form' => trans('db_dx_users.person_code'),
            'max_lenght' => 12,
            'is_required' => $arr_vals['person_code_required']
        ]);                                   
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => $row_type_id, 'is_readonly' => $is_read_only]);
        App\Libraries\DBHelper::addFieldToView($list_id, $view->id, $fld_id);

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
            'db_name' => 'full_name_code',
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
