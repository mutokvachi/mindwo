<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxHdRequestsUi extends Migration
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
            $list_name = "IT pieteikumi";
            $item_name = "IT pieteikums";

             // create register
            $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
            $list_gen = new Structure\StructMethod_register_generate();
            $list_gen->obj_id = $obj_id;
            $list_gen->register_title = $list_name;
            $list_gen->form_title = $item_name;
            $list_gen->doMethod();

            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;                    
                    
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
            $user_fld = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'reporter_empl_id')->first()->id;
            DB::table('dx_roles_lists')->insert(['role_id' => 35, 'list_id' => $list_id, 'user_field_id' => $user_fld, 'is_edit_rights' => 0, 'is_delete_rights' => 0, 'is_new_rights' => 0]); // Sys admins
            
            // make menu
            DB::table('dx_menu')->insertgetId(['title'=>$list_name, 'list_id'=>$list_id, 'order_index' => 18, 'fa_icon' => 'fa fa-question-circle']);
            
            //fix user field (because we have 2 registers in 1 table dx_users)
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'responsible_empl_id')
                    ->update([
                        'rel_list_id'=>Config::get('dx.employee_list_id'),
                        'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id,
                        'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_LOOKUP
                    ]);
            
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
            
            // remove workflow status field from UI
            \App\Libraries\DBHelper::removeFieldsFromAllForms('dx_hd_requests', ['dx_item_status_id'], false);
            \App\Libraries\DBHelper::removeFieldsFromAllViews('dx_hd_requests', ['dx_item_status_id'], false);
            
            // tune file field
            \App\Libraries\DBHelper::removeFieldCMS('dx_hd_requests', 'file_guid');

            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name', '=', 'file_name')
            ->update([
                'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_FILE
            ]);
            
            // add hints
            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name', '=', 'responsible_empl_id')
            ->update([
                'hint' => 'Atbildīgo personu uzstāda darbplūsma. Tā mainās atkarībā no pieteikuma risināšanas soļa.'
            ]);
            
            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name', '=', 'status_time')
            ->update([
                'hint' => 'Statusa laiku uzstāda darbplūsma atkarībā no pieteikuma risināšanas gaitas.'
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
                        'row_type_id' => 3
                    ]);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'request_time')
                                                ->first()->id)
                    ->update([
                        'row_type_id' => 3
                    ]);
             
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'request_type_id')
                                                ->first()->id)
                    ->update(['group_label' => 'Problēma']);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'inner_type_id')
                                                ->first()->id)
                    ->update(['group_label' => 'Risinājums']);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'responsible_empl_id')
                                                ->first()->id)
                    ->update(['is_readonly' => 1]);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'status_time')
                                                ->first()->id)
                    ->update(['is_readonly' => 1]);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'status_id')
                                                ->first()->id)
                    ->update([
                        'row_type_id' => 2
                    ]);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'status_time')
                                                ->first()->id)
                    ->update([
                        'row_type_id' => 2,
                        'is_readonly' => 1
                    ]);
            
            // add custom JavaScript to form
            \App\Libraries\DBHelper::addJavaScriptToForm($table_name, '2017_02_17_dx_requests.js', 'Pielāgo lauku platumus un izlīdzina lauku grupu nosaukumus');
            
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
            \App\Libraries\DBHelper::deleteRegister('dx_hd_requests');
            DB::table('dx_objects')->where('db_name', '=', 'dx_hd_requests')->delete();
        });
    }
}
