<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersSalariesAddJobTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users_salaries', function (Blueprint $table) {
            $table->string('job_title', 500)->nullable()->comment = 'Job title';            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users_salaries', function (Blueprint $table) {           
            $table->dropColumn(['job_title']);
        });
    }
}
