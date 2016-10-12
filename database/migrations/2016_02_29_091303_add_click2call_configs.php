<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClick2callConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_config')->insert([
            ['config_name' => 'CLICK2CALL_URL', 'config_hint' => 'Ceļš uz Click2Call vietni. Tālruņa numura parametrs vērība jānorāda #phone# - tā tiks aizvietota ar reālo tālruņa numuru.',  'field_type_id' => 1, 'val_varchar' => 'http://www.latvenergo.lv?phone=#phone#'],
            ['config_name' => 'CLICK2CALL_INNER_PHONE', 'config_hint' => 'Organizācijas iekšējā tālruņa numura sākuma daļa. Tiks izmantots, lai pievienotu 4 zīmju numuriem.',  'field_type_id' => 1, 'val_varchar' => '123']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_config')->where('config_name', '=', 'CLICK2CALL_URL')->delete();
        DB::table('dx_config')->where('config_name', '=', 'CLICK2CALL_INNER_PHONE')->delete();
    }
}
