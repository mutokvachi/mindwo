<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsMaterialsCreate extends Migration
{
    private $table_name = "edu_subjects_materials";
    
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
            
            $table->integer('subject_id')->unsigned()->comment = trans('db_' . $this->table_name.'.subject_id');
            $table->integer('material_id')->unsigned()->comment = trans('db_' . $this->table_name.'.material_id');
            
            $table->index('subject_id');            
            $table->foreign('subject_id')->references('id')->on('edu_subjects')->onDelete('cascade');
            
            $table->index('material_id');            
            $table->foreign('material_id')->references('id')->on('edu_materials');
            
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
