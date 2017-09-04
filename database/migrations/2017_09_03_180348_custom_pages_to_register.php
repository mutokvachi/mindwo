<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CustomPagesToRegister extends Migration
{
    private $table_name = "dx_pages";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            foreach(trans('db_' . $this->table_name . '.custom_pages') as $page) {
                $page['is_active'] = 1;
                $page_id = DB::table('dx_pages')->insertGetId($page);
                DB::table('dx_roles_pages')->insert(['role_id' => 1, 'page_id' => $page_id]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () {            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            foreach(trans('db_' . $this->table_name . '.custom_pages') as $page) {
                $page = DB::table('dx_pages')->where('title','=', $page['title'])->first();

                if ($page) {                   
                    DB::table('dx_roles_pages')->where('page_id', '=', $page->id)->delete();
                    DB::table('dx_pages')->where('id', '=', $page->id)->delete();
                }
            }
        });
    }
}
