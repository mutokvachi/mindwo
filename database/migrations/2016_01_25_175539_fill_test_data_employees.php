<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillTestDataEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update in_employees set office_address = 'Rīga, Pulkveža Brieža iela 12', email=concat('test', id, '@latvenergo.lv'), phone='29131987', start_date='2011-05-05', source_id=1");
        
        DB::table('in_employees')->where('employee_name', 'like', 'Jānis%')->update(['position' => 'Projektu vadītājs', 'department' => 'Klientu apkalpošanas daļa', 'picture_name' => 'janis_picture.jpg', 'picture_guid' => 'b48347a0-9fb9-40a8-8e5a-08e586978a08.jpg']);
        DB::table('in_employees')->where('employee_name', 'like', 'Ilga%')->update(['position' => 'Vecākais inženieris', 'department' => 'Releju nodaļa', 'picture_name' => 'inga_picture.jpg', 'picture_guid' => 'e65c07de-bb5b-4370-a6ad-851247ab0ebf.jpg']);
        DB::table('in_employees')->whereNull('position')->update(['position' => 'Klientu konsultants', 'department' => 'Pārdošanas daļa', 'source_id' => 2]);
        
        DB::update("update in_employees set employee_name=concat('Jānis Zariņš', ' ', id), start_date='2008-06-05', source_id=3 where picture_name='janis_picture.jpg'");
        DB::update("update in_employees set employee_name=concat('Inga Lejniece', ' ', id), start_date='2004-06-05', source_id=2 where picture_name='inga_picture.jpg'");
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
