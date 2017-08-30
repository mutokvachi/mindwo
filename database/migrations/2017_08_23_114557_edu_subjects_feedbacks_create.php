<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsFeedbacksCreate extends Migration
{
    private $table_name = "edu_subjects_feedbacks";
    
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
            
            $table->integer('subject_id')->unsigned()->comment = 'Mācību pasākums';

            $table->string('author', 250)->nullable()->comment = 'Autors';
            $table->string('email', 250)->nullable()->comment = 'E-pasts';
            $table->text('text')->nullable()->comment = 'Atsauksmes teksts';
            $table->boolean('is_published')->nullable()->comment = 'Personas veids';
            
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
