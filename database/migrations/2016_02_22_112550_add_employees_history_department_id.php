<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmployeesHistoryDepartmentId extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_employees_history', function (Blueprint $table) {
            $table->integer('old_department_id')->nullable()->unsigned()->comment = "Iepriekšējais departaments";
            $table->index('old_department_id');
            $table->foreign('old_department_id')->references('id')->on('in_departments')->onDelete('cascade');

            $table->integer('new_department_id')->nullable()->unsigned()->comment = "Jaunais departaments";
            $table->index('new_department_id');
            $table->foreign('new_department_id')->references('id')->on('in_departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_employees_history', function (Blueprint $table) {
            $table->dropForeign(['old_department_id', 'new_department_id']);
            $table->dropColumn(['old_department_id', 'new_department_id']);
        });
    }
}