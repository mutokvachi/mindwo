<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxTablesFieldsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_tables_fields');
        
        Schema::create('dx_tables_fields', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('table_name', 200)->comment = trans('dx_tables_fields.title');
            $table->string('field_name', 100)->comment = trans('dx_tables_fields.field_name');            
            $table->integer('field_type_id')->comment = trans('dx_tables_fields.field_type_id');
            
            $table->index('field_type_id');            
            $table->foreign('field_type_id')->references('id')->on('dx_field_types');
            
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
        Schema::dropIfExists('dx_tables_fields');
    }
}
