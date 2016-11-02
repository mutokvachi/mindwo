<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates database structure for user's personal documents
 */
class CreateEmployeeDocs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {      
        Schema::create('in_personal_docs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('name', 250)->nullable()->comment = "Name";
            $table->string('description', 1000)->nullable()->comment = "Description";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
         Schema::create('in_employees_personal_docs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('doc_nr', 500)->nullable()->comment = "Document's number";
            $table->date('valid_to')->nullable()->comment = "Valid to";
            $table->string('publisher', 500)->nullable()->comment = "Document's publisher";
            $table->string('file_name', 500)->nullable()->comment = "Files's name";
            $table->string('file_guid', 50)->nullable()->comment = "File's unique identifier";
            
            $table->integer('user_id')->nullable()->comment = "User";            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users')->onDelete('cascade');
            
            $table->integer('doc_id')->nullable()->unsigned()->comment = "Personal document";            
            $table->index('doc_id');            
            $table->foreign('doc_id')->references('id')->on('in_personal_docs')->onDelete('cascade');
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('in_personal_docs_countries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('country_id')->nullable()->unsigned()->comment = "Country";            
            $table->index('country_id');            
            $table->foreign('country_id')->references('id')->on('dx_countries')->onDelete('cascade');
            
            $table->integer('doc_id')->nullable()->unsigned()->comment = "Personal document";            
            $table->index('doc_id');            
            $table->foreign('doc_id')->references('id')->on('in_personal_docs')->onDelete('cascade');            
            
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
        Schema::dropIfExists('in_employees_personal_docs');
        Schema::dropIfExists('in_personal_docs_countries');
        Schema::dropIfExists('in_personal_docs');        
    }
}
