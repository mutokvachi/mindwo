<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduSubjectsRemovePrices extends EduMigration
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
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'price_for_teacher');  
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'price_for_rooms'); 
            
            $frm = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            $fld = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'price_for_student')->first();
            DB::table('dx_forms_fields')->where('form_id', '=', $frm->id)->where('field_id', '=', $fld->id)->update([
                'group_label' => null
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
    }
}
