<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduTimeFieldsUi extends EduMigration
{   
    private $tbls = ['edu_subjects_groups_days', 'edu_subjects_groups_days_pauses', 'edu_subjects_groups_days_teachers'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {       
        DB::transaction(function () {
            
            foreach($this->tbls as $table_name) {
                $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;  
                
                DB::table('dx_lists_fields')
                        ->where('list_id', '=', $list_id)
                        ->where('db_name', '=', 'time_from')
                        ->update([
                            'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_TIME,
                            'default_value' => '9:00'
                        ]);
                
                DB::table('dx_lists_fields')
                        ->where('list_id', '=', $list_id)
                        ->where('db_name', '=', 'time_to')
                        ->update([
                            'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_TIME,
                            'default_value' => '17:00'
                        ]);
                
            }
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
            
            foreach($this->tbls as $table_name) {
                $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;  
                
                DB::table('dx_lists_fields')
                        ->where('list_id', '=', $list_id)
                        ->where('db_name', '=', 'time_from')
                        ->update([
                            'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                            'default_value' => null
                        ]);
                
                DB::table('dx_lists_fields')
                        ->where('list_id', '=', $list_id)
                        ->where('db_name', '=', 'time_to')
                        ->update([
                            'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                            'default_value' => null
                        ]);
                
            }
        });
    }
}
