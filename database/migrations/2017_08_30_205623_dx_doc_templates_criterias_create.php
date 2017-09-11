<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxDocTemplatesCriteriasCreate extends Migration
{
    private $table_name = "dx_doc_templates_criterias";
    
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
            $table->integer('template_id')->unsigned()->comment = trans('db_' . $this->table_name.'.template_id');
            $table->integer('list_id')->comment = trans('db_' . $this->table_name.'.list_id');
            $table->integer('field_id')->nullable()->comment = trans('db_' . $this->table_name.'.field_id');
            $table->integer('operation_id')->nullable()->comment = trans('db_' . $this->table_name.'.operation_id');
            $table->string('criteria', 50)->nullable()->comment = trans('db_' . $this->table_name.'.criteria');
            
            $table->index('list_id');            
            $table->foreign('list_id')->references('id')->on('dx_lists')->onDelete('cascade');
            
            $table->index('template_id');            
            $table->foreign('template_id')->references('id')->on('dx_doc_templates')->onDelete('cascade');

            $table->index('field_id');            
            $table->foreign('field_id')->references('id')->on('dx_lists_fields');

            $table->index('operation_id');            
            $table->foreign('operation_id')->references('id')->on('dx_field_operations');
            
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
