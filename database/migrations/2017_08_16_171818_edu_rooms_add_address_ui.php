<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduRoomsAddAddressUi extends EduMigration
{
    private $table_name = "edu_rooms";
    
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

            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'room_address',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_' . $this->table_name . '.room_address'),
                'title_form' => trans('db_' . $this->table_name . '.room_address'),
                'max_lenght' => 250,
                'hint' => trans('db_' . $this->table_name . '.room_address_hint')
            ]);
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id);         
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
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'room_address');           
        });
    }
}