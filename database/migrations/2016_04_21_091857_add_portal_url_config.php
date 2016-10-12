<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPortalUrlConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       DB::table('dx_config')
                ->insert(['config_name' => 'PORTAL_PUBLIC_URL', 
                    'config_hint' => 'Portāla publiskās daļas adrese (URL). URL jānoslēdz ar "/"',
                    'field_type_id' => 1,
                    'val_varchar' => 'http://local.vk/']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_config')
                ->where('config_name', 'PORTAL_PUBLIC_URL')
                ->delete();
    }
}
