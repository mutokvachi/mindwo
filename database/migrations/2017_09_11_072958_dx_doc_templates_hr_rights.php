<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\HRMigration;

class DxDocTemplatesHrRights extends HRMigration
{
    private $table_name = "dx_doc_templates";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function hr_up()
    {
        DB::transaction(function () {            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => $this->RoleHR_id, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1, 'is_import_rights' => 0, 'is_view_rights' => 0]); // Sys admins
           
            DB::table('dx_menu')->whereNotNull('role_id')->update(['role_id' => null]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function hr_down()
    {
        DB::transaction(function () {            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            DB::table('dx_roles_lists')
            ->where('list_id', '=', $list_id)
            ->where('role_id', '=', $this->RoleHR_id)
            ->delete();
        });
    }
}
