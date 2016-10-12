<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveObligatorySourceInSystem extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_systems', function ($table) {
           $table->dropForeign(['employee_id']);
        });
        
        Schema::table('in_systems', function ($table) {
            $table->integer('employee_id')->unsigned()->nullable()->change();
            $table->foreign('employee_id')->references('id')->on('in_employees');
            $table->string('url', 250)->nullable()->change();
        });
        
        Schema::table('in_incidents', function (Blueprint $table) {
            $table->dropColumn('planned_resolve_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {        
        Schema::table('in_incidents', function (Blueprint $table) {
            $table->datetime('planned_resolve_time')->nullable()->comment = "Plānotais novēršanas datums un laiks";
        });
    }
}