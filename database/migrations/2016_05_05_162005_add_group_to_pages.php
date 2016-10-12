<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupToPages extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        
        Schema::table('dx_pages', function (Blueprint $table) {
            $table->integer('group_id')
                    ->nullable()
                    ->unsigned()
                    ->index()                    
                    ->comment = "Grupa";
            
            $table->foreign('group_id')->references('id')->on('dx_menu_groups');
        });
        
        DB::table('dx_pages')->update(['group_id' => 2]);        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {        
        Schema::table('dx_pages', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn(['group_id']);
        });
    }
}
