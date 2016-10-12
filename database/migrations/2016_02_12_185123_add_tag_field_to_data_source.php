<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTagFieldToDataSource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_sources', function (Blueprint $table) {
            $table->integer('tag_id')->nullable()->unsigned()->comment = "Raksturīgā iezīme";

            $table->index('tag_id');
            $table->foreign('tag_id')->references('id')->on('in_tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_sources', function (Blueprint $table) {
            $table->dropColumn('tag_id');
        });
    }
}
