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
            
            $table->string('title', 250)->nullable()->comment = trans('db_' . $this->table_name.'.title');
            $table->integer('module_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.module_id');
            $table->integer('subject_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.subject_id');
            
            $table->string('file_pdf_name', 500)->comment = trans('db_' . $this->table_name.'.file_pdf_name');
            $table->string('file_pdf_guid', 50)->comment = trans('db_' . $this->table_name.'.file_pdf_guid');
      
            $table->string('file_word_name', 500)->comment = trans('db_' . $this->table_name.'.file_word_name');
            $table->string('file_word_guid', 50)->comment = trans('db_' . $this->table_name.'.file_word_guid');
                      
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
