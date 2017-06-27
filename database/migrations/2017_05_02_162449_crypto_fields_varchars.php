<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CryptoFieldsVarchars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users_shares', function (Blueprint $table) {
            $table->string('shares', 1000)->nullable()->default(null)->change();
        });
        
        Schema::table('dx_users_salaries', function (Blueprint $table) {
            $table->string('salary', 1000)->nullable()->default(null)->change();
            $table->string('annual_salary', 1000)->nullable()->default(null)->change();
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
