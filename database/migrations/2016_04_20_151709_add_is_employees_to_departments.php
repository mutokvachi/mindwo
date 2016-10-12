<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsEmployeesToDepartments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_departments', function (Blueprint $table) {
            $table->boolean('is_employees')
                    ->default(0)
                    ->index()                    
                    ->comment = "Ir darbinieki";
        });
        
        DB::update('update in_departments as d set is_employees = 1 where exists (select id from in_employees where department_id = d.id)');
        DB::update('update in_departments as d set is_employees = 0 where not exists (select id from in_employees where department_id = d.id)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_departments', function (Blueprint $table) {
            $table->dropColumn(['is_employees']);
        });
    }
}
