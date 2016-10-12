<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourcesRights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->integer('source_id')->nullable()->comment = "Datu avots";
                        
            $table->index('source_id');
            $table->foreign('source_id')->references('id')->on('in_sources');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropForeign(['source_id']);   
            $table->dropColumn(['source_id']);
        });
    }
}
