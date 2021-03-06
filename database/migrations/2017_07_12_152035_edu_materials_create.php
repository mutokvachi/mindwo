<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduMaterialsCreate extends Migration
{
    private $table_name = "edu_materials";
    
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
            $table->text('description')->nullable()->comment = trans('db_' . $this->table_name.'.description');
            $table->string('file_name', 500)->nullable()->comment = trans('db_' . $this->table_name.'.file_name');
            $table->string('file_guid', 50)->nullable()->comment = trans('db_' . $this->table_name.'.file_guid');
            $table->text('file_dx_text')->nullable()->comment = trans('db_' . $this->table_name.'.file_dx_text');
            $table->string('author', 200)->nullable()->comment = trans('db_' . $this->table_name.'.author');
            $table->boolean('is_published')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_published');
            
            $table->integer('org_id')->nullable()->unsigned()->comment = trans('db_' . $this->table_name.'.org_id');
            $table->integer('teacher_id')->nullable()->comment = trans('db_' . $this->table_name.'.teacher_id');
            $table->boolean('is_public_access')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_public_access');
                        
            $table->index('org_id');            
            $table->foreign('org_id')->references('id')->on('edu_orgs');
            
            $table->index('teacher_id');            
            $table->foreign('teacher_id')->references('id')->on('dx_users');
            
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
