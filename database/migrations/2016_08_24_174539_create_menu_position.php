<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuPosition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('dx_menu_position', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->nullable()->comment = "Nosaukums";
            $table->string('code', 10)->nullable()->comment = "Kods";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::table('dx_menu', function (Blueprint $table) {
            $table->integer('position_id')->nullable()->unsigned()->comment = "Pozīcija";
            
            $table->index('position_id');
            $table->foreign('position_id')->references('id')->on('dx_menu_position');
        });
        
        DB::table('dx_menu_position')->insert(['id' => 1, 'title' => 'Kreisā josla', 'code' => 'left']);
        DB::table('dx_menu_position')->insert(['id' => 2, 'title' => 'Augšējā josla', 'code' => 'top']);
        
        DB::table('dx_menu')->update(['position_id'=>1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_menu', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn(['position_id']);
        });
        
        Schema::dropIfExists('dx_menu_position');
    }
}
