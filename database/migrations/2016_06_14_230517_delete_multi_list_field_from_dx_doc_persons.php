<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteMultiListFieldFromDxDocPersons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_doc_persons', function (Blueprint $table) {
            $table->dropForeign(['multi_list_id']);
            $table->dropColumn(['multi_list_id']);
        });
        
        Schema::table('dx_doc_persons', function (Blueprint $table) {
            $table->integer('list_id')->nullable()->comment = "Reģistrs";
            
            $table->index('list_id');
            $table->foreign('list_id')->references('id')->on('dx_lists');
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
            $table->integer('multi_list_id')->nullable()->comment = "Reģistrs";
            
            $table->index('multi_list_id');
            $table->foreign('multi_list_id')->references('id')->on('dx_lists');
        });
        
        Schema::table('dx_doc_persons', function (Blueprint $table) {
            $table->dropForeign(['list_id']);
            $table->dropColumn(['list_id']);
        });        
        
    }
}
