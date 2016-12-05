<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates order index column for employee's time off types table
 */
class AddTimeoffTypesOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_timeoff_types', function (Blueprint $table) {
            $table->integer('order_index')->nullable()->default(0)->comment = "Secība";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_timeoff_types', function (Blueprint $table) {        
            $table->dropColumn(['order_index']);
        });
    }
}
