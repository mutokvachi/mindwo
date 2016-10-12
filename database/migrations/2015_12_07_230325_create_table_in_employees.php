<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_name', 200)->nullable();
            $table->datetime('birth_date')->nullable();
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            $table->index('birth_date');
            $table->index('employee_name');
        });
        
        // Fill default web text values
        DB::table('in_employees')->insert([
            ['employee_name' => 'Kārlis Zariņš', 'birth_date' => '1978-12-07'],
            ['employee_name' => 'Elīna Celma', 'birth_date' => '1948-12-07'],
            ['employee_name' => 'Osvalds Osis', 'birth_date' => '1988-12-07'],
            ['employee_name' => 'Sigals Rīds', 'birth_date' => '1995-12-07']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_employees');
    }
}
