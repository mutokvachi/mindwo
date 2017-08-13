<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduOrgsUsersUi extends EduMigration
{
    private $table_name = "edu_orgs_users";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {       
        
        DB::transaction(function () {
            
            $list_name = trans('db_' . $this->table_name . '.list_name');
            $item_name = trans('db_' . $this->table_name . '.item_name');

             // create register
            $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $this->table_name, 'title' => $list_name , 'is_history_logic' => 1]);
            $list_gen = new Structure\StructMethod_register_generate();
            $list_gen->obj_id = $obj_id;
            $list_gen->register_title = $list_name;
            $list_gen->form_title = $item_name;
            $list_gen->doMethod();

            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;       

            // reorganize view fields - hide or remove unneeded
            \App\Libraries\DBHelper::removeFieldsFromAllViews($this->table_name, ['id'], true); // hide ID field                       
            \App\Libraries\DBHelper::removeFieldsFromAllForms($this->table_name, ['id'], false);
            
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1, 'is_import_rights' => 1, 'is_view_rights' => 1]); // Sys admins            
            
            // fix user_id lookup
            $user_list = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_all'))->first();            
            $user_display = DB::table('dx_lists_fields')->where('list_id', '=', $user_list->id)->where('db_name', '=', 'full_name_code')->first();
            
            DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'user_id')->update([
                'rel_list_id' => $user_list->id,
                'rel_display_field_id' => $user_display->id
            ]);
            
            $this->addTab($list_id, trans('db_dx_users.list_title_edu'));
            $this->addTab($list_id, trans('db_dx_users.list_title_org'));
            $this->addTab($list_id, trans('db_dx_users.list_title_teacher'));
            $this->addTab($list_id, trans('db_dx_users.list_title_serv'));
            $this->addTab($list_id, trans('db_dx_users.list_title_student'));
            
            // update fields
            App\Libraries\DBHelper::updateFormField($list_id, "phone", ['row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "mobile", ['row_type_id' => 2]);
            
            // remove unneded fields from view
            App\Libraries\DBHelper::removeFieldsFromAllViews($list_id, [
                'email',
                'phone',
                'embeded',
                'mobile',
            ], false);
            
            $def_view_id = DB::table('dx_views')
                    ->where('list_id', '=', $list_id)
                    ->where('title', '=', trans('db_' . $this->table_name . '.list_name'))
                    ->first()
                    ->id;
            
            DB::table('dx_views')
                    ->where('id', '=', $def_view_id)
                    ->update([
                        'view_type_id' => 9,
                        'custom_sql' => "
                            SELECT * FROM (
                                select
                                        edu_orgs_users.id,
                                        edu_orgs_users.user_id,
                                        edu_orgs_users.org_id,
                                        edu_orgs_users.job_title,
                                        edu_orgs_users.email,
                                        edu_orgs_users.phone,
                                        edu_orgs_users.mobile,
                                        edu_orgs_users.end_date,
                                        edu_orgs.title as edu_orgs_2_title
                                from
                                        edu_orgs_users
                                        left join edu_orgs on edu_orgs_users.org_id = edu_orgs.id
                                where     
                                        user_id = [ITEM_ID] AND
                                        (
                                        exists(select id from dx_users_roles ru where ru.role_id in (1, 74) and ru.user_id=[ME])
                                        or
                                        org_id in (select org_id from edu_orgs_users where user_id = [ME] and ifnull(end_date, DATE_ADD(now(), INTERVAL 1 DAY)) >=now())
                                        )
                                ) tb 
                            WHERE 1 = 1
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
            \App\Libraries\DBHelper::deleteRegister($this->table_name);
            DB::table('dx_objects')->where('db_name', '=', $this->table_name)->delete();
        });
    }
    
    private function addTab($list_id, $list_name) {
        $subj_list = DB::table('dx_lists')->where('list_title', '=', $list_name)->first();
        $form = DB::table('dx_forms')->where('list_id', '=', $subj_list->id)->first();
        $subj_field = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'user_id')->first();

        $tab_main_id = DB::table('dx_forms_tabs')->insertGetId([
            'form_id' => $form->id,
            'title' => trans('db_dx_users.tab_orgs'),
            'is_custom_data' => 0,
            'order_index' => 30,
            'grid_list_id' => $list_id,
            'grid_list_field_id' => $subj_field->id
        ]);
    }
}
