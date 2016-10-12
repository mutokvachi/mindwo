<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConfigForEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_config')->insert([
            ['config_name' => 'WRITEQ_NOTIFY_EMAILS', 'config_hint' => 'E-pastu adreses, uz kurām tiks nosūtīta notifikācija par jaunu uzdotu jautājumu sadaļā Raksti mums. Norādot vairakus e-pastus, tie jāatdala ar semikolonu.',  'field_type_id' => 1, 'val_varchar' => 'janis.supe@gmail.com']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_config')->where('config_name', '=', 'WRITEQ_NOTIFY_EMAILS')->delete();
    }
}
