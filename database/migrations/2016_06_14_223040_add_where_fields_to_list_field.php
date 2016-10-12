<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWhereFieldsToListField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->integer('operation_id')->nullable()->comment = "Operācija";
            $table->string('criteria', 2000)->nullable()->comment = "Kritērijs";
            
            $table->index('operation_id');
            $table->foreign('operation_id')->references('id')->on('dx_field_operations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->dropForeign(['operation_id']);
            $table->dropColumn(['operation_id']);
            $table->dropColumn(['criteria']);
        });
    }
}
