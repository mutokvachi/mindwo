<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMultipleMarkInDxListsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->boolean('is_multiple_files')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->dropColumn('is_multiple_files');
        });
    }
}
