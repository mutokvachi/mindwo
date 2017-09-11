<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\HRMigration;

class HrPagesRights extends HRMigration
{
    private $table_name = "dx_pages";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function hr_up()
    {
        DB::transaction(function () {            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            foreach(trans('db_' . $this->table_name . '.custom_pages') as $page) {
                
                if ($page['url_title'] != 'calendar/scheduler' && 
                    $page['url_title'] != 'calendar/complect' &&
                    $page['url_title'] != 'constructor' &&
                    $page['url_title'] != 'constructor/register/create' &&
                    $page['url_title'] != 'constructor/menu/1') {
                        
                    $pg = DB::table('dx_pages')->where('title','=', $page['title'])->first();
                    
                    if ($pg) {                                           
                        DB::table('dx_roles_pages')->insert(['role_id' => (($page['url_title'] == 'mail') ? $this->RoleHR_emails_id : $this->RoleHR_id), 'page_id' => $pg->id]);
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function hr_down()
    {
        DB::transaction(function () {            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            foreach(trans('db_' . $this->table_name . '.custom_pages') as $page) {
                if ($page['url_title'] != 'calendar/scheduler' && 
                    $page['url_title'] != 'calendar/complect' &&
                    $page['url_title'] != 'constructor' &&
                    $page['url_title'] != 'constructor/register/create' &&
                    $page['url_title'] != 'constructor/menu/1') {

                    $pg = DB::table('dx_pages')->where('title','=', $page['title'])->first();

                    if ($pg) {                   
                        DB::table('dx_roles_pages')
                        ->where('page_id', '=', $pg->id)
                        ->where('role_id', '=', $this->RoleHR_id)
                        ->delete();

                        DB::table('dx_roles_pages')
                        ->where('page_id', '=', $pg->id)
                        ->where('role_id', '=', $this->RoleHR_emails_id)
                        ->delete();
                    }
                }
            }
        });
    }
}
