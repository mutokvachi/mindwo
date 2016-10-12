<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmployeeNrToInEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_employees', function (Blueprint $table) {
            $table->string('code', 10)->nullable();
            $table->integer('manager_id')->nullable();
            
            $table->index('code');
            $table->index('manager_id');
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
            $table->dropColumn('code');
            $table->dropColumn('manager_id');
        });
    }
}
