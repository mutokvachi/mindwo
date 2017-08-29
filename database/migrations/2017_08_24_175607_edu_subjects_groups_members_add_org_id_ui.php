<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduSubjectsGroupsMembersAddOrgIdUi extends EduMigration
{
    private $table_name = "edu_subjects_groups_members";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {       
        
        DB::transaction(function () {
            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;       

            $org_list = DB::table('dx_lists')
                        ->where('list_title', '=', trans('db_edu_orgs.list_name'))
                        ->first();
            
            $org_fld = DB::table('dx_lists_fields')
                       ->where('list_id', '=', $org_list->id)
                       ->where('db_name', '=', 'title')
                       ->first();
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'org_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'title_list' => trans('db_' . $this->table_name . '.org_id'),
                'title_form' => trans('db_' . $this->table_name . '.org_id'),
                'rel_list_id' => $org_list->id,
                'rel_display_field_id' => $org_fld->id
            ]);
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id); 
            
            App\Libraries\DBHelper::reorderFormField($list_id, 'org_id', 'group_id');
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
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'org_id');           
        });
    }
}
