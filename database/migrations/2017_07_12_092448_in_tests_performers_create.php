<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InTestsPerformersCreate extends Migration
{
    private $table_name = "in_tests_performers";
    
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists($this->table_name);
        
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');            
            
            $table->integer('user_id')->comment = trans('db_' . $this->table_name.'.user_id');
            $table->integer('module_id')->unsigned()->comment = trans('db_' . $this->table_name.'.module_id');
            $table->integer('subject_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.subject_id');
            $table->integer('test_id')->unsigned()->comment = trans('db_' . $this->table_name.'.test_id');
            $table->datetime('perform_start')->nullable()->comment = trans('db_' . $this->table_name.'.perform_start');
            $table->datetime('perform_end')->nullable()->comment = trans('db_' . $this->table_name.'.perform_end');
            $table->integer('total_questions')->nullable()->default(0)->comment = trans('db_' . $this->table_name.'.total_questions');
            $table->integer('correct_answers')->nullable()->default(0)->comment = trans('db_' . $this->table_name.'.correct_answers');
            $table->boolean('is_ok')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_ok');
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users');
            
            $table->index('module_id');            
            $table->foreign('module_id')->references('id')->on('edu_modules');
            
            $table->index('subject_id');            
            $table->foreign('subject_id')->references('id')->on('edu_subjects');
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table_name);
    }
}
