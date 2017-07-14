<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduProgrammsCreate extends Migration
{
    private $table_name = "edu_programms";
    
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
            
            $table->string('title', 250)->comment = trans('db_' . $this->table_name.'.title');
            $table->string('sub_title', 250)->comment = trans('db_' . $this->table_name.'.sub_title');
            $table->integer('parent_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.parent_id');
            $table->integer('avail_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.avail_id');
            $table->integer('icon_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.icon_id');
            $table->text('description')->nullable()->comment = trans('db_' . $this->table_name.'.description');
            $table->integer('needs_survey_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.needs_survey_id');
            $table->integer('qualify_test_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.qualify_test_id');
            $table->integer('cert_numerator_id')->nullable()->comment = trans('db_' . $this->table_name.'.cert_numerator_id');
            
            $table->integer('is_published')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_published');
            
            $table->index('icon_id');            
            $table->foreign('icon_id')->references('id')->on('dx_icons_files');
            
            $table->index('avail_id');            
            $table->foreign('avail_id')->references('id')->on('edu_programms_avail');
            
            $table->index('needs_survey_id');            
            $table->foreign('needs_survey_id')->references('id')->on('in_tests');
            
            $table->index('qualify_test_id');            
            $table->foreign('qualify_test_id')->references('id')->on('in_tests');
            
            $table->index('parent_id');            
            $table->foreign('parent_id')->references('id')->on($this->table_name);
            
            $table->index('cert_numerator_id');            
            $table->foreign('cert_numerator_id')->references('id')->on('dx_numerators');
            
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
