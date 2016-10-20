<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixNdaLenghtForDxUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // fix guid fields for nda & other files
        Schema::table('dx_users', function (Blueprint $table) {            
            $table->string('nda_file_guid', 50)->nullable()->change();
            $table->string('other_file_guid', 50)->nullable()->change();
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
            $table->string('nda_file_guid', 20)->nullable()->change();
            $table->string('other_file_guid', 20)->nullable()->change();
        });
    }
}
