<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Adds new column to user table which is country used to associate documents to
 */
class AddUserDocCountry extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->integer('doc_country_id')->nullable()->unsigned()->comment = "Documents country";
            $table->index('doc_country_id');
            $table->foreign('doc_country_id')->references('id')->on('dx_countries');
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
            $table->dropForeign(['doc_country_id']);
            $table->dropIndex(['doc_country_id']);
            $table->dropColumn('doc_country_id');
        });
    }
}
