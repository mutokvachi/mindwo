<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxDocAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('dx_doc', 'amount1')) {
            return true;
        }
        
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->decimal('amount1', 9, 2)->default(0)->nullable()->comment = 'Summa';
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
            $table->dropColumn(['amount1']);
        });
    }
}
