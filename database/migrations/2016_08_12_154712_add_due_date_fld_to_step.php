<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDueDateFldToStep extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_workflows', function (Blueprint $table) {
            $table->integer('due_field_id')->nullable()->comment = "Termiņa lauks no dokumenta";
            
            $table->index('due_field_id');
            $table->foreign('due_field_id')->references('id')->on('dx_lists_fields');
            
            $table->integer('resolution_field_id')->nullable()->comment = "Rezolūcijas lauks no dokumenta";
            
            $table->index('resolution_field_id');
            $table->foreign('resolution_field_id')->references('id')->on('dx_lists_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_workflows', function (Blueprint $table) {
            $table->dropForeign(['due_field_id']);
            $table->dropColumn(['due_field_id']);
            $table->dropForeign(['resolution_field_id']);
            $table->dropColumn(['resolution_field_id']);
        });
    }
}
