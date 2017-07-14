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
            $table->boolean('is_embeded')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_embeded');
            $table->string('file_name', 500)->nullable()->comment = trans('db_' . $this->table_name.'.file_name');
            $table->string('file_guid', 50)->nullable()->comment = trans('db_' . $this->table_name.'.file_guid');
            $table->text('file_text')->nullable()->comment = trans('db_' . $this->table_name.'.file_text');
            $table->text('embeded')->nullable()->comment = trans('db_' . $this->table_name.'.embeded');
            $table->string('author', 200)->nullable()->comment = trans('db_' . $this->table_name.'.author');
            $table->integer('org_id')->unsigned()->comment = trans('db_' . $this->table_name.'.org_id');
            $table->boolean('is_public_access')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_public_access');
            $table->boolean('is_published')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_published');
            
            $table->index('org_id');            
            $table->foreign('org_id')->references('id')->on('edu_orgs');
            
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
