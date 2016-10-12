<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInProcesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_processes', function (Blueprint $table) {
            $table->string('arguments')->nullable();
            $table->string('get_method', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('in_documents', function (Blueprint $table) {        
            $table->dropColumn(['arguments','get_method']);      
        });
    }
}
