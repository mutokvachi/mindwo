<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexFieldToDxMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_menu', function (Blueprint $table) {
            $table->string('title_index', 200)->nullable();
        });
        
        DB::statement("update dx_menu set title_index=REPLACE(CONCAT('[',order_index,'] ',title),'-','!')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_menu', function (Blueprint $table) {
            $table->dropColumn('title_index');
        });
    }
}
