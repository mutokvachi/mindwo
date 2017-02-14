<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxFieldOperationsAddIsCriteria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_field_operations', function (Blueprint $table) {
            $table->boolean('is_criteria')->nullable()->default(true)->comment = 'Is criteria value';
        });
        
        DB::table('dx_field_operations')->whereIn('id', [6, 7, 9, 11])->update(['is_criteria' => 0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_field_operations', function (Blueprint $table) {            
            $table->dropColumn(['is_criteria']);
        });
    }
}
