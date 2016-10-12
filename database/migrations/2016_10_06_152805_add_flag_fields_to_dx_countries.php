<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFlagFieldsToDxCountries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_countries', function (Blueprint $table) {
            $table->string('flag_file_name', 100)->nullable();
			$table->string('flag_file_guid', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_countries', function (Blueprint $table) {
			$table->dropColumn([ 'flag_file_name', 'flag_file_guid' ]);
        });
    }
}
