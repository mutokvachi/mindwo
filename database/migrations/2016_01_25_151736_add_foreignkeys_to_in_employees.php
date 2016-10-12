<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignkeysToInEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_employees', function (Blueprint $table) {
            $table->foreign('left_reason_id')->references('id')->on('in_left_reasons');
            $table->foreign('substit_empl_id')->references('id')->on('in_employees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_employees', function (Blueprint $table) {
            $table->dropForeign('in_employees_left_reason_id_foreign');
            $table->dropForeign('in_employees_substit_empl_id_foreign');
        });
    }
}
