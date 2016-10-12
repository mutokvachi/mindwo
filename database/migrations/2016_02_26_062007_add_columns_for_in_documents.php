<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Papildina Lotus Notes dokumentu tabulu
 */
class AddColumnsForInDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_doc_departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200)->nullable()->comment = "Nodaļas nosaukums";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::table('in_documents', function (Blueprint $table) {
            $table->uuid('unid')->nullable()->comment = "Dokumenta unikālais identifikators";

            $table->string('noteid', 50)->nullable();
            $table->integer('siblings')->nullable();
            $table->decimal('version', 6, 2)->nullable()->comment = "Versijas numurs";

            $table->integer('doc_department_id')->nullable()->unsigned()->comment = "Nodaļa";
            $table->index('doc_department_id');
            $table->foreign('doc_department_id')->references('id')->on('in_doc_departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_doc_departments');
        
        Schema::table('in_documents', function (Blueprint $table) {
            $table->dropForeign(['doc_department_id']);            
            $table->dropColumn(['unid','noteid','siblings','version','doc_department_id']);      
        });
    }
}
