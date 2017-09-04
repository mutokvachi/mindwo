<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class DxEmailsSentEduUi extends EduMigration
{
    private $table_name = "dx_emails_sent";
    
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

            // fix students related grid ID
            $student_list = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_all'))->first();            
            $student_display = DB::table('dx_lists_fields')->where('list_id', '=', $student_list->id)->where('db_name', '=', 'full_name_code')->first();
            
            DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'user_id')->update([
                'rel_list_id' => $student_list->id,
                'rel_display_field_id' => $student_display->id
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
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            $empl_list_id = Config::get('dx.employee_list_id', 0);
            if ($empl_list_id) {         
                $fld_display = DB::table('dx_lists_fields')->where('list_id', '=', $empl_list_id)->where('db_name', '=', 'display_name')->first();
                
                DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'user_id')->update([
                    'rel_list_id' => $empl_list_id,
                    'rel_display_field_id' => $fld_display->id
                ]);
            }       
        });
    }
}
