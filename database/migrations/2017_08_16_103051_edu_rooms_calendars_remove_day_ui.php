<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduRoomsCalendarsRemoveDayUi extends EduMigration
{
    private $table_name = "edu_rooms_calendars";
    
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

            App\Libraries\DBHelper::removeFieldCMS($list_id, 'subject_group_day_id');           
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
