<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxDocTemplatesCreate extends Migration
{
    private $table_name = "dx_doc_templates";
    
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
    
            $table->integer('list_id')->comment = trans('db_' . $this->table_name.'.list_id');
            $table->string('title', 250)->comment = trans('db_' . $this->table_name.'.title');
            $table->integer('kind_id')->default(1)->unsigned()->comment = trans('db_' . $this->table_name.'.kind_id');
            $table->string('file_name', 500)->nullable()->comment = trans('db_' . $this->table_name.'.file_name');
            $table->string('file_guid', 50)->nullable()->comment = trans('db_' . $this->table_name.'.file_guid');
            $table->string('description', 1000)->nullable()->comment = trans('db_' . $this->table_name.'.description');
            $table->integer('numerator_id')->nullable()->comment = trans('db_' . $this->table_name.'.numerator_id');
            $table->string('title_file', 100)->nullable()->comment = trans('db_' . $this->table_name.'.title_file');
            
            $table->text('html_template')->nullable()->comment = trans('db_' . $this->table_name.'.html_template');

            $table->index('list_id');            
            $table->foreign('list_id')->references('id')->on('dx_lists')->onDelete('cascade');
            
            $table->index('numerator_id');            
            $table->foreign('numerator_id')->references('id')->on('dx_numerators');

            $table->index('kind_id');            
            $table->foreign('kind_id')->references('id')->on('dx_doc_templates_kinds');
            
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
