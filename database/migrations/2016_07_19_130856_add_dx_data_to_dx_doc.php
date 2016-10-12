<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDxDataToDxDoc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('dx_doc', 'data_id'))
        {
            Schema::table('dx_doc', function (Blueprint $table) {
                $table->integer('data_id')->nullable()->comment = "Saistītais ieraksts";

                $table->index('data_id');
                $table->foreign('data_id')->references('id')->on('dx_data');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->dropForeign(['data_id']);
            $table->dropColumn(['data_id']);
        });
    }
}
