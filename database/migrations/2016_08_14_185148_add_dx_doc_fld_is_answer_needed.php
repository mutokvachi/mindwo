<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDxDocFldIsAnswerNeeded extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->boolean('is_answer_needed')->nullable()->comment = "Vai nepieciešama atbilde";
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
            $table->dropColumn(['is_answer_needed']);
        });
    }
}
