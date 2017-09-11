<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduActivitiesDrop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $arr=['edu_modules_activities', 'edu_modules_students_activities', 'edu_activities'];
        
        DB::transaction(function () use ($arr) {
            foreach($arr as $tabl) {
                \App\Libraries\DBHelper::deleteRegister($tabl);
                DB::table('dx_objects')->where('db_name', '=', $tabl)->delete();
            }            
        });

        foreach($arr as $tabl) {
            Schema::dropIfExists($tabl);
        }        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
