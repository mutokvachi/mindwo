<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableInWeather extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_weather', function (Blueprint $table) {
            $table->increments('id');
            $table->date('weather_date');
            $table->integer('weather_type_id');
            $table->integer('temper_low')->nullable();
            $table->integer('temper_high')->nullable();
            $table->string('meteo_code', 100)->nullable();
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('weather_type_id');
            $table->index('meteo_code');
            $table->unique('weather_date');
        });
        
        // Fill default values
        DB::table('in_weather')->insert([
            ['weather_date' => '2015-12-08', 'weather_type_id' => 1, 'temper_low' => '-2', 'temper_high' => '2'],
            ['weather_date' => date('Y-n-d'), 'weather_type_id' => 1, 'temper_low' => '-4', 'temper_high' => '1']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_weather');
    }
}
