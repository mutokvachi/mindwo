<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduMenusReorganize extends EduMigration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {       
        
        DB::transaction(function () {
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_edu_programms.list_name'))
                    ->update([
                        'head_title' => trans('db_edu_programms.head_title')
                    ]);
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_edu_modules_students.list_name'))
                    ->update([
                        'head_title' => trans('db_edu_modules_students.head_title')
                    ]);
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_edu_materials.list_name'))
                    ->update([
                        'head_title' => trans('db_edu_materials.head_title')
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
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_edu_programms.list_name'))
                    ->update([
                        'head_title' => null
                    ]);
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_edu_modules_students.list_name'))
                    ->update([
                        'head_title' => null
                    ]);
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_edu_materials.list_name'))
                    ->update([
                        'head_title' => null
                    ]);
        });
    }
}
