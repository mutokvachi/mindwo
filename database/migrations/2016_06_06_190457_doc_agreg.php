<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DocAgreg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_doc_agreg_kinds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->nullable()->comment = "Nosaukums";            
            $table->integer('list_id')->nullable()->comment = "Reģistrs";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('list_id');
            $table->foreign('list_id')->references('id')->on('dx_lists');
        });    
        
        Schema::create('dx_doc_agreg', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('list_id')->nullable()->comment = "Reģistrs";
            $table->integer('item_id')->nullable()->comment = "Ieraksts ID";
            
            $table->integer('kind_id')->nullable()->unsigned()->comment = "Dokumenta veids";
            
            $table->date('reg_date')->nullable()->comment = "Reģistrēšanas datums";
            $table->string('reg_nr', 100)->nullable()->comment = "Reģistrēšanas numurs";
            $table->string('reg_nr_client', 100)->nullable()->comment = "Reģistrēšanas numurs (otras puses)";            
            $table->text('description')->nullable()->comment = "Apraksts";
            $table->string('file_name', 100)->nullable()->comment = "Datnes nosaukums";
            $table->string('file_guid', 100)->nullable()->comment = "Datnes GUID";
            $table->text('file_text')->nullable()->comment = "Datnes teksts";
            
            $table->integer('person1_id')->nullable()->comment = "Sadarbības partneris 1";
            $table->string('person1_title', 500)->nullable()->comment = "Sadarbības partneris 1 - nosaukums";
            
            $table->integer('person2_id')->nullable()->comment = "Sadarbības partneris 2";
            $table->string('person2_title', 500)->nullable()->comment = "Sadarbības partneris 2 - nosaukums";
            
            $table->integer('person3_id')->nullable()->comment = "Sadarbības partneris 3";
            $table->string('person3_title', 500)->nullable()->comment = "Sadarbības partneris 3 - nosaukums";
            
            $table->integer('employee_id')->nullable()->comment = "Darbinieks";
            $table->string('employee_title', 200)->nullable()->comment = "Darbinieka nosaukums";
            
            $table->integer('source_id')->nullable()->comment = "Datu avots";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('person1_id');
            $table->index('person1_title');
            
            $table->index('person2_id');
            $table->index('person2_title');
            
            $table->index('person3_id');
            $table->index('person3_title');
            
            $table->index('employee_id');
            $table->index('employee_title');
            
            $table->index('item_id');
            
            $table->index('list_id');
            $table->foreign('list_id')->references('id')->on('dx_lists');
            
            $table->index('source_id');
            $table->foreign('source_id')->references('id')->on('in_sources');
            
            $table->index('kind_id');
            $table->foreign('kind_id')->references('id')->on('dx_doc_agreg_kinds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_doc_agreg');
        Schema::dropIfExists('dx_doc_agreg_kinds');
    }
}
