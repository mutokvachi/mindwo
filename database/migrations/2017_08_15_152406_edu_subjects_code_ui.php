<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduSubjectsCodeUi extends EduMigration
{
    private $table_name = "edu_subjects";
    
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

            $fld_code = DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'subject_code')
                    ->first();
            
            DB::table('dx_lists_fields')
                    ->where('id', '=', $fld_code->id)
                    ->update([
                        'title_list' => trans('db_' . $this->table_name . '.subject_code'),
                        'title_form' => trans('db_' . $this->table_name . '.subject_code'),
                        'hint' => trans('db_' . $this->table_name . '.subject_code_hint'),
                        'is_required' => 0
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

            $fld_code = DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'subject_code')
                    ->first();
            
            DB::table('dx_lists_fields')
                    ->where('id', '=', $fld_code->id)
                    ->update([
                        'title_list' => trans('db_' . $this->table_name . '.subject_code'),
                        'title_form' => trans('db_' . $this->table_name . '.subject_code'),
                        'hint' => trans('db_' . $this->table_name . '.subject_code_hint'),
                        'is_required' => 1
            ]); 
        });
    }
}
