<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxHdRequestsUi2 extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            $table_name = "dx_hd_requests";
            $list_name = "Mani IT pieteikumi";
            $item_name = "IT pieteikums";

             // create register
            $obj_id = DB::table('dx_objects')->where('db_name', '=', $table_name)->first()->id;
            $list_gen = new Structure\StructMethod_register_generate();
            $list_gen->obj_id = $obj_id;
            $list_gen->register_title = $list_name;
            $list_gen->form_title = $item_name;
            $list_gen->doMethod();

            // get list
            $list_id = $this->getList()->id;                   
              
            // Self tasks performing role
            $user_fld = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'reporter_empl_id')->first()->id;
            DB::table('dx_roles_lists')->insert(['role_id' => 35, 'list_id' => $list_id, 'user_field_id' => $user_fld, 'is_edit_rights' => 0, 'is_delete_rights' => 0, 'is_new_rights' => 1]); // Sys admins
            
            // Superadmins rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'user_field_id' => $user_fld, 'is_edit_rights' => 0, 'is_delete_rights' => 0, 'is_new_rights' => 1]); // Sys admins
                        
            //fix user field (because we have 2 registers in 1 table dx_users)
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'reporter_empl_id')
                    ->update([
                        'rel_list_id'=>Config::get('dx.employee_list_id'),
                        'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id,
                        'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_LOOKUP,
                        'default_value' => '[ME]'
                    ]);
            
            // set request time default to NOW
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'request_time')
                    ->update([
                        'default_value' => '[NOW]'
                    ]);
            
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'status_time')
                    ->update([
                        'default_value' => '[NOW]'
                    ]);
            
            // adjust relation to tree classifier
            $tree_list_id = App\Libraries\DBHelper::getListByTable('dx_hd_request_types')->id;
            $parent_id =  DB::table('dx_lists_fields')
                ->where('list_id', '=', $tree_list_id)
                ->where('db_name', '=', 'parent_id')
                ->first()
                ->id;
            
            DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'request_type_id')
                ->update([
                    'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_MULTILEVEL,
                    'rel_parent_field_id' => $parent_id
                ]);
            
            // HTML field to simple text
            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name', '=', 'description')
            ->update([
                'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_LONG_TEXT,
                'max_lenght' => 4000
            ]);
            
            // remove fields
            \App\Libraries\DBHelper::removeFieldCMS($list_id, 'dx_item_status_id');
            \App\Libraries\DBHelper::removeFieldCMS($list_id, 'inner_type_id');
            \App\Libraries\DBHelper::removeFieldCMS($list_id, 'responsible_empl_id');
            \App\Libraries\DBHelper::removeFieldCMS($list_id, 'status_time');
            \App\Libraries\DBHelper::removeFieldCMS($list_id, 'status_id');
            
            // tune file field
            \App\Libraries\DBHelper::removeFieldCMS($list_id, 'file_guid');

            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name', '=', 'file_name')
            ->update([
                'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_FILE
            ]);
            
            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name', '=', 'file_name')
            ->update([
                'hint' => 'Datnes laukā var pievienot, piemēram, ekrānšāviņu ar kļūdas paziņojumu.'
            ]);
            
            // add sections info and adjust form fields          
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'id')
                                                ->first()->id)
                    ->update([
                        'group_label' => 'Pieteikums',
                        'row_type_id' => 3
                    ]);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'reporter_empl_id')
                                                ->first()->id)
                    ->update([
                        'row_type_id' => 3,
                        'is_readonly' => 1
                    ]);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'request_time')
                                                ->first()->id)
                    ->update([
                        'row_type_id' => 3,
                        'is_readonly' => 1
                    ]);
             
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'request_type_id')
                                                ->first()->id)
                    ->update(['group_label' => 'Problēma']);
            
            // add custom JavaScript to form
            \App\Libraries\DBHelper::addJavaScriptToForm($list_id, '2017_02_17_dx_requests.js', 'Pielāgo lauku platumus un izlīdzina lauku grupu nosaukumus');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = $this->getList();
        DB::transaction(function () use ($list) {
           \App\Libraries\DBHelper::deleteRegister($list->id);
        });
    }
    
    private function getList() {
        return DB::table('dx_lists')->where('list_title', '=', 'Mani IT pieteikumi')->first();
    }
}
