<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduSubjectsGroupsJs extends EduMigration
{
    private $table_name = "edu_subjects_groups";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {
        $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;  
                
        DB::transaction(function () use ($list_id){
            \App\Libraries\DBHelper::addJavaScriptToForm($list_id, '2017_09_06_edu_subjects_groups.js', trans('db_' . $this->table_name . '.form_js'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function edu_down()
    {   
        $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;  

        DB::transaction(function () use ($list_id){
            \App\Libraries\DBHelper::removeJavaScriptFromForm($list_id, trans('db_' . $this->table_name . '.form_js'));
            

        });   
    }
}
