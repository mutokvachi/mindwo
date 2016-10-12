<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaskSubstitInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_tasks', function (Blueprint $table) {
            $table->text('substit_info')->nullable()->comment = "Aizvietošana";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_tasks', function (Blueprint $table) {
            $table->dropColumn(['substit_info']);
        });
    }
}
