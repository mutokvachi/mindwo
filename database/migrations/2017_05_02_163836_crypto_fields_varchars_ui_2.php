<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CryptoFieldsVarcharsUi2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_users_salaries');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){
            
            DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'salary')->update([
                'type_id' => 1,
                'max_lenght' => 1000,
                'is_crypted' => 1
            ]);
            
            DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'annual_salary')->update([
                'type_id' => 1,
                'max_lenght' => 1000,
                'is_crypted' => 1
            ]);
        });
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
