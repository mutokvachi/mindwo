<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CustomPhpAddDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_custom_php', function (Blueprint $table) {
            $table->text('description')->nullable()->comment = "Apraksts";            	
        });
        
        Schema::table('dx_pages', function (Blueprint $table) {
            $table->text('description')->nullable()->comment = "Apraksts";            	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_custom_php', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
        
        Schema::table('dx_pages', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
    }
}
