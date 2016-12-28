<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxDocFilesCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('dx_doc_files')) {
            return;
        }
        
        Schema::create('dx_doc_files', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('doc_id')->unsigned()->comment = "Document";  
            $table->integer('list_id')->comment = "Register";
            $table->string('file_name', 500)->nullable()->comment = "File name";
            $table->string('file_guid')->nullable()->comment = "File GUID";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('doc_id');            
            $table->foreign('doc_id')->references('id')->on('dx_doc')->onDelete('cascade');
            
            $table->index('list_id');            
            $table->foreign('list_id')->references('id')->on('dx_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_doc_files');
    }
}
