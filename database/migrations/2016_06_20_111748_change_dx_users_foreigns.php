<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDxUsersForeigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->dropForeign(['empl_creator_id']);
            $table->dropForeign(['empl_signer_id']);
        });
        
        Schema::table('dx_doc', function (Blueprint $table) {
            
            $table->integer('empl_creator_id')->nullable()->unsigned(false)->change();
            $table->integer('empl_signer_id')->nullable()->unsigned(false)->change();
            
            $table->foreign('empl_creator_id')->references('id')->on('dx_users');
            
            $table->foreign('empl_signer_id')->references('id')->on('dx_users');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
