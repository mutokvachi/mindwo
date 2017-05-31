<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxHdRequestsMyUi extends Migration
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
            $dub_list = DB::table('dx_lists')->where('object_id', '=', $obj->id)->where('list_title', '=', 'Mani IT pieteikumi')->first();
            
            $list_copy = new Structure\StructMethod_register_copy();
            $list_copy->obj_id = $obj->id;
            $list_copy->list_id = $dub_list->id;
            $list_copy->register_title = 'Mani biroja atbalsta pieteikumi';
            $list_copy->doMethod();

            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_copy->new_list_id, 'is_edit_rights' => 0, 'is_delete_rights' => 0, 'is_new_rights' => 1]); // Sys admins
            DB::table('dx_roles_lists')->insert(['role_id' => 35, 'list_id' => $list_copy->new_list_id, 'is_edit_rights' => 0, 'is_delete_rights' => 0, 'is_new_rights' => 1]); // My tasks performers
            
            $def_list = DB::table('dx_lists')->where('object_id', '=', $obj->id)->where('list_title', '=', 'Biroja atbalsta pieteikumi')->first();
            
// update list_id
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_copy->new_list_id)
                    ->where('db_name', '=', 'list_id')
                    ->update([
                        'default_value' => $def_list->id,
                        'operation_id' => 1,
                        'criteria' => $def_list->id
                    ]);
            
            // update relations
            $list_types = DB::table('dx_lists')->where('list_title', '=', 'Pieteikumu veidi - biroja atbalsts')->first();
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_copy->new_list_id)
                    ->where('db_name', '=', 'request_type_id')
                    ->update([
                        'rel_list_id' => $list_types->id,
                        'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_types->id)->where('db_name', '=', 'title')->first()->id
                    ]);

            $list_types = DB::table('dx_lists')->where('list_title', '=', 'Prioritātes - biroja atbalsts')->first();
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_copy->new_list_id)
                    ->where('db_name', '=', 'priority_id')
                    ->update([
                        'rel_list_id' => $list_types->id,
                        'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_types->id)->where('db_name', '=', 'title')->first()->id
                    ]);
            
            $list_types = DB::table('dx_lists')->where('list_title', '=', 'Pieteikumu tipi - biroja atbalsts')->first();
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_copy->new_list_id)
                    ->where('db_name', '=', 'inner_type_id')
                    ->update([
                        'rel_list_id' => $list_types->id,
                        'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_types->id)->where('db_name', '=', 'title')->first()->id
                    ]);
            
            $list_types = DB::table('dx_lists')->where('list_title', '=', 'Darbu statusi - biroja atbalsts')->first();
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_copy->new_list_id)
                    ->where('db_name', '=', 'status_id')
                    ->update([
                        'rel_list_id' => $list_types->id,
                        'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_types->id)->where('db_name', '=', 'title')->first()->id
                    ]);
                        
             \App\Libraries\DBHelper::addJavaScriptToForm($list_copy->new_list_id, '2017_05_29_dx_hd_requests_office.js', 'Pielāgo lauku platumus un izlīdzina lauku grupu nosaukumus');
            
            
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
                    ->where('list_title', '=', 'Mani biroja atbalsta pieteikumi')
                    ->first();                        
                        
            \App\Libraries\DBHelper::deleteRegister($list->id);
            
        });
    }
    
    private function isHelpDeskUI() {
        $klasif_menu = DB::table('dx_menu')->where('title', '=', 'Biroja atbalsts')->first();
          
        return ($klasif_menu) ? true : false;
    }
}
