<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConfigForSlidesTransition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_config')->insert([
            'config_name' => 'TOP_SLIDE_TRANSITION_TIME', 
            'config_hint' => 'TOP ziņu slīdrādes slaidu nomainīšanās intervāls milisekundēs. Lai norādītu, piemēram, 5 sekundes, jāievada 5000.',
            'field_type_id' => 5,
            'val_integer' => 10000
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_config')->where('config_name','=','TOP_SLIDE_TRANSITION_TIME')->delete();
    }
}
