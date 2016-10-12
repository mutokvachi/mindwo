<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableInWeatherTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_weather_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->nullable();
            $table->string('meteo_code', 100)->nullable();
            $table->string('file_name', 500)->nullable();
            $table->string('file_guid', 50)->nullable();
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            $table->index('meteo_code');
        });
        
        DB::table('in_weather_types')->insert([
            ['title' => 'Saulains un sniegs', 'file_name' => 'w_saule_sniegs.gif', 'file_guid' => 'w_saule_sniegs.gif'],
            ['title' => 'Apmācies, stiprs sniegs', 'file_name' => 'w_saule_sniegs.gif', 'file_guid' => 'w_saule_sniegs.gif']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::dropIfExists('in_weather_types');
    }
}
