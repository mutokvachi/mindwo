<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMenuGroupField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_menu_groups', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('title', 500)->nullable()->comment = "Nosaukums";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
        });
        
        DB::table('dx_menu_groups')->insert([
            ['title' => 'SVS'],
            ['title' => 'Portāls'],
        ]);
        
        Schema::table('dx_menu', function (Blueprint $table) {
            $table->integer('group_id')
                    ->nullable()
                    ->unsigned()
                    ->index()                    
                    ->comment = "Grupa";
            
            $table->foreign('group_id')->references('id')->on('dx_menu_groups');
        });
        
        DB::table('dx_menu')->whereNull('parent_id')->update(['group_id' => 1]);        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {        
        Schema::table('dx_menu', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn(['group_id']);
        });
        
        Schema::dropIfExists('dx_menu_groups');
    }
}
