<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInEmployeeHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('in_employees_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->nullable();
            $table->integer('old_source_id')->nullable();
            $table->integer('new_source_id')->nullable();
            $table->string('old_position', 500)->nullable();
            $table->string('new_position', 500)->nullable();
            $table->string('old_department', 4000)->nullable();
            $table->string('new_department', 4000)->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('employee_id');
            $table->index('old_source_id');
            $table->index('new_source_id');
            $table->index('valid_from');
        });
        
        DB::table('in_employees_history')->insert([
            ['employee_id' => 1, 'old_source_id' => 1, 'new_source_id' => 1, 'old_position' => 'Projektu vadītājs', 'new_position' => 'Projektu pārvaldnieks', 'valid_from' => date('Y-m-d'), 'old_department' => 'Loģistikas nodaļa', 'new_department' => 'Transporta nodaļa'],
            ['employee_id' => 2, 'old_source_id' => 1, 'new_source_id' => 1, 'old_position' => 'Inženieris', 'new_position' => 'Vecākais inženieris', 'valid_from' => date('Y-m-d'), 'old_department' => 'Loģistikas nodaļa', 'new_department' => 'Transporta nodaļa'],
            ['employee_id' => 3, 'old_source_id' => 1, 'new_source_id' => 1, 'old_position' => 'Ražošanas pārzinis', 'new_position' => 'Ražošanas nodaļas vadītājs', 'valid_from' => date('Y-m-d'), 'old_department' => null, 'new_department' => null],
            ['employee_id' => 4, 'old_source_id' => 1, 'new_source_id' => 1, 'old_position' => 'Projektu vadītājs', 'new_position' => 'Projektu pārvaldnieks', 'valid_from' => date('Y-m-d'), 'old_department' => 'Loģistikas nodaļa', 'new_department' => 'Transporta nodaļa'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_employees_history');
    }
}
