<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;

class EduPagesRights extends EduMigration
{
    private $table_name = "dx_pages";
    
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
            
            foreach(trans('db_' . $this->table_name . '.custom_pages') as $page) {
               
                $page = DB::table('dx_pages')->where('title','=', $page['title'])->first();
                
                if ($page) {                   
                    DB::table('dx_roles_pages')->insert(['role_id' => self::ROLE_MAIN, 'page_id' => $page->id]);
                }
            }
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
            
            foreach(trans('db_' . $this->table_name . '.custom_pages') as $page) {
                $page = DB::table('dx_pages')->where('title','=', $page['title'])->first();

                if ($page) {                   
                    DB::table('dx_roles_pages')
                    ->where('page_id', '=', $page->id)
                    ->where('role_id', '=', self::ROLE_MAIN)
                    ->delete();
                }
            }
        });
    }
}
