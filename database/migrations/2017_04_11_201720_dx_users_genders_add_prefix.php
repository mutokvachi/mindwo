<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersGendersAddPrefix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users_genders', function (Blueprint $table) {
            $table->string('person_title', 20)->nullable()->comment = 'Person title';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users_genders', function (Blueprint $table) {
            $table->dropColumn(['person_title']);
        });
    }
}
