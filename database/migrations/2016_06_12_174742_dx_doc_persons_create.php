<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxDocPersonsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_doc_persons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('multi_list_id')->nullable()->comment = 'Reģistrs';
            
            $table->date('reg_date')->nullable()->comment = "Reģistrēšanas datums";
            $table->string('reg_nr', 100)->nullable()->comment = "Reģistrēšanas numurs";
            
            $table->date('received_date')->nullable()->comment = "Saņemšanas datums";
            $table->string('reg_nr_client', 100)->nullable()->comment = "Reģistrēšanas numurs (otras puses)";            
                        
            $table->string('file_name', 100)->nullable()->comment = "Datnes nosaukums";
            $table->string('file_guid', 100)->nullable()->comment = "Datnes GUID";
            $table->text('file_dx_text')->nullable()->comment = "Datnes teksts";
            
            $table->integer('person1_id')->nullable()->unsigned()->comment = "Korespondents 1";            
            $table->integer('person2_id')->nullable()->unsigned()->comment = "Korespondents 2";            
            $table->integer('person3_id')->nullable()->unsigned()->comment = "Korespondents 3";
            
            $table->string('about', 1000)->nullable()->comment = "Par ko";
            $table->string('address', 500)->nullable()->comment = "Adrese";
            $table->integer('pages_count')->nullable()->unsigned()->comment = "Lapu skaits";
            $table->integer('copy_count')->nullable()->default(1)->unsigned()->comment = "Eksemplāru skaits";
            $table->integer('doc_type_id')->nullable()->unsigned()->comment = "Dokumenta veids";
            
            $table->date('due_date')->nullable()->comment = "Izpildes termiņš";
            $table->string('resolution_text', 1000)->nullable()->comment = "Rezolūcija";
            $table->string('resolution_file_name', 100)->nullable()->comment = "Rezolūcijas datne";
            $table->string('resolution_file_guid', 100)->nullable()->comment = "Rezolūcijas datnes GUID";
            
            $table->boolean('is_on_control')->nullable()->default(0)->comment = "Ir uz kontroli";
            
            $table->text('notes')->nullable()->comment = "Piezīmes";
            $table->integer('answer_doc_id')->nullable()->unsigned()->comment = "Atbildes dokuments";
            
            $table->integer('source_id')->nullable()->comment = "Struktūrvienība";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->unique('reg_nr');
                        
            $table->index('multi_list_id');
            $table->foreign('multi_list_id')->references('id')->on('dx_lists');
            
            $table->index('person1_id');
            $table->foreign('person1_id')->references('id')->on('dx_persons');
            
            $table->index('person2_id');
            $table->foreign('person2_id')->references('id')->on('dx_persons');
            
            $table->index('person3_id');
            $table->foreign('person3_id')->references('id')->on('dx_persons');
            
            $table->index('doc_type_id');
            $table->foreign('doc_type_id')->references('id')->on('dx_doc_agreg_kinds');
            
            $table->index('source_id');
            $table->foreign('source_id')->references('id')->on('in_sources');
            
        });
        
        Schema::table('dx_doc_persons', function (Blueprint $table) {
            $table->index('answer_doc_id');
            $table->foreign('answer_doc_id')->references('id')->on('dx_doc_persons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_doc_persons', function (Blueprint $table) {
            $table->dropForeign(['answer_doc_id']);
        });
        
        Schema::dropIfExists('dx_doc_persons');
    }
}
