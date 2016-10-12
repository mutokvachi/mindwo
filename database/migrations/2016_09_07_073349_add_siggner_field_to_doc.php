<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSiggnerFieldToDoc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->integer('empl_signer2_id')->nullable()->comment = "Otrais parakstītājs";

            $table->index('empl_signer2_id');
            $table->foreign('empl_signer2_id')->references('id')->on('dx_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->dropForeign(['empl_signer2_id']);
            $table->dropColumn(['empl_signer2_id']);
        });
    }
}
