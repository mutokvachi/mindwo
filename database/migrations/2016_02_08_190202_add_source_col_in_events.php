<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceColInEvents extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_events', function (Blueprint $table) {
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
        Schema::table('in_events', function (Blueprint $table) {
            $table->dropForeign(['source_id']);
        });
        Schema::table('in_events', function (Blueprint $table) {
            $table->dropColumn('source_id');
        });
    }

}
