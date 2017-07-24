<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduOrgsUi extends EduMigration
{
    private $table_name = "edu_orgs";
    
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
            
            // menu
            $parent_menu = DB::table('dx_menu')->where('title', '=', trans('db_dx_menu.lbl_classifiers'))->first();
            $arr_params = [
                'menu_list_id' => $list_id, 
                'list_title' => trans('db_' . $this->table_name . '.list_name'),
                'menu_parent_id' => $parent_menu->id,
            ];
            App\Libraries\DBHelper::makeMenu($arr_params);
            
            App\Libraries\DBHelper::removeFieldsFromAllViews($list_id, [
                'org_type_id',
                'reg_nr',
                'address',
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
                                        edu_orgs.id as id,
                                        edu_orgs.title as edu_orgs_title,
                                        title,
                                        org_type_id
                                        reg_nr,
                                        address
                                        
                                from
                                        edu_orgs
                                where                                        
                                        (
                                        exists(select id from dx_users_roles ru where ru.role_id in (1, 74) and ru.user_id=[ME])
                                        or
                                        id in (select org_id from edu_orgs_users where user_id = [ME] and ifnull(end_date, DATE_ADD(now(), INTERVAL 1 DAY)) >=now())
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
}
