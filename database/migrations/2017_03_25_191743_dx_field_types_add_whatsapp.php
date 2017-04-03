<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxFieldTypesAddWhatsapp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function (){
            DB::table('dx_field_types')->insert([
               'id' => 20,
               'title' => 'WhatsApp',
               'is_max_lenght' => 1,
               'sys_name' => 'whatsapp',
               'height_px' => 34
            ]);
            
            DB::table('dx_lists_fields')->where('db_name', '=', 'whatsapp')->update(['type_id' => 20]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () use ($list_id){            
            DB::table('dx_lists_fields')->where('db_name', '=', 'whatsapp')->update(['type_id' => 1]);
            DB::table('dx_field_types')->where('id', '=', 20)->delete();
        });
    }
}
