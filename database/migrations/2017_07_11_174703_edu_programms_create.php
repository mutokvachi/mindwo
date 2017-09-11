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
            
            $table->integer('dx_supervise_id')->unsigned()->comment = trans('db_' . $this->table_name.'.dx_supervise_id');
            $table->string('title', 250)->comment = trans('db_' . $this->table_name.'.title');
            $table->string('code', 3)->comment = trans('db_' . $this->table_name.'.code');
            $table->boolean('is_meta_required')->default(false)->comment = trans('db_' . $this->table_name.'.is_meta_required');
            $table->string('color', 50)->nullable()->comment = trans('db_' . $this->table_name.'.color');
            $table->text('description')->nullable()->comment = trans('db_' . $this->table_name.'.description');
            $table->text('user_approval_msg')->nullable()->comment = trans('db_' . $this->table_name.'.user_approval_msg');            
            $table->integer('is_published')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_published');
                        
            $table->index('dx_supervise_id');            
            $table->foreign('dx_supervise_id')->references('id')->on('dx_supervise');
            
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
