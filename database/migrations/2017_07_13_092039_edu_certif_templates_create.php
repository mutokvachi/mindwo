<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduCertifTemplatesCreate extends Migration
{
    private $table_name = "edu_certif_templates";
    
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
            
            $table->string('title', 300)->nullable()->comment = trans($this->table_name.'.title');
            $table->integer('programm_id')->unsigned()->nullable()->comment = trans($this->table_name.'.programm_id');
            $table->integer('subject_id')->unsigned()->nullable()->comment = trans($this->table_name.'.subject_id');
            $table->text('content')->nullable()->comment = trans($this->table_name.'.content');
            
            $table->index('programm_id');            
            $table->foreign('programm_id')->references('id')->on('edu_programms');
            
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
