<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceIdToDxPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_pages', function (Blueprint $table) {
            $table->integer('source_id')->nullable();
            $table->index('source_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_pages', function (Blueprint $table) {
            $table->dropColumn('source_id');
        });
    }
}
