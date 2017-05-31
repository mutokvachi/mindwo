<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InEmployeesPersonalDocsUiCascade extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        
        $list = App\Libraries\DBHelper::getListByTable('in_employees_personal_docs');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){
            
            DB::table('dx_lists')->where('id', '=', $list_id)->update(['is_cascade_delete' => 1]);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('in_employees_personal_docs');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
                
        DB::transaction(function () use ($list_id){            
           DB::table('dx_lists')->where('id', '=', $list_id)->update(['is_cascade_delete' => 0]);
        });
    }
}
