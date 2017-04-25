<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxCountriesAddPhone extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_countries', function (Blueprint $table) {
            $table->string('phone_code', 5)->nullable()->comment = trans('db_dx_countries.phone_code');
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
            $table->dropColumn(['phone_code']);
        });
    }
}
