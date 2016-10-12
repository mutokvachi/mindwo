<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableInSystems extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::create('in_systems', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 200)->comment = "Nosaukums";
            $table->string('url', 250)->comment = "Interneta adrese";
            $table->string('picture_name', 500)->nullable()->comment = "Attēla nosaukums";
            $table->string('picture_guid', 50)->nullable()->comment = "Attēla identifikators";
            $table->integer('source_id')->nullable()->comment = "Datu avots";
            $table->integer('employee_id')->unsigned()->comment = "Atbildīgais darbinieks";

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();

            $table->index('source_id');
            $table->foreign('source_id')->references('id')->on('in_sources');

            $table->index('employee_id');
            $table->foreign('employee_id')->references('id')->on('in_employees');
        });

        DB::table('in_incidents')->delete();
        
        Schema::table('in_incidents', function (Blueprint $table) {
            $table->integer('created_user_id')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();

            $table->datetime('planned_resolve_time')->nullable()->comment = "Plānotais novēršanas datums un laiks";

            $table->integer('system_id')->unsigned()->comment = "Sistēma";
            $table->index('system_id');
            $table->foreign('system_id')->references('id')->on('in_systems');

            // Dzēš lieku kolonnu, jo tagad ir tieša atsauce uz sistēmu tabulu
            $table->dropColumn('sys_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_systems');

        Schema::table('in_incidents', function (Blueprint $table) {
            $table->string('sys_name', 500)->nullable()->comment = "Sistēmas nosaukums";
            $table->dropColumn(['created_user_id', 'modified_user_id', 'modified_time', 'planned_resolve_time', 'system_id']);
        });
    }

}
