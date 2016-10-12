<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCabinetToInEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_employees', function (Blueprint $table) {
            $table->string('office_cabinet', 10)->nullable();
        });
        
        DB::update("update in_employees set office_cabinet='212' where picture_name='janis_picture.jpg'");
        DB::update("update in_employees set office_cabinet='187' where picture_name='inga_picture.jpg'");
        DB::update("update in_employees set office_cabinet='12' where office_cabinet is null");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_employees', function (Blueprint $table) {
            $table->dropColumn('office_cabinet');
        });
    }
}
