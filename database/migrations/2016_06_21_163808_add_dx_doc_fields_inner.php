<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDxDocFieldsInner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->decimal('ammount', 2)->nullable()->default(0)->comment = "Summa";
            
            $table->string('version_nr', 20)->nullable()->comment = "Versijas numurs";
            $table->boolean('is_in_force')->nullable()->default(true)->comment = "Vai ir spēkā";
            $table->date('decision_date')->nullable()->comment = "Lēmuma datums";
            
            $table->integer('rel_doc_id')->nullable()->unsigned()->comment = "Saistītais dokuments";
            
            $table->index('rel_doc_id');
            $table->foreign('rel_doc_id')->references('id')->on('dx_doc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->dropColumn(['version_nr']);
            $table->dropColumn(['is_in_force']);
            $table->dropColumn(['decision_date']);
            
            $table->dropForeign(['rel_doc_id']);
            $table->dropColumn(['rel_doc_id']);
        });
    }
}
